<?php

namespace Da\User\Controller;

use yii\helpers\FormatConverter;
use Da\User\Helper\UserEntityHelper;
use Da\User\Module;
use Webauthn\CeremonyStep\CeremonyStepManager;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\CeremonyStep\CheckAllowedOrigins;
use Webauthn\TrustPath\EmptyTrustPath;
use Da\User\Model\UserEntity;
use Da\User\Model\User;
use Da\User\Repository\MyPublicKeyCredentialSourceRepository;
use Webauthn\CollectedClientData;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorData;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Controller;

class UserEntityController extends Controller
{
    public $enableCsrfValidation = true;
    public $userEntityHelper;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userEntityHelper = new UserEntityHelper();
    }


    public function challengeGeneration(): array
    {
        $passkeys = UserEntity::find()->where(['type' => 'public-key'])->all();

        if (empty($passkeys)) {
            return ['success' => false, 'message' => 'No passkey registered'];
        }

        $credentialDescriptors = array_map(fn($pk) => [
            'type' => 'public-key',
            'id' => $pk->credential_id,
        ], $passkeys);

        $challengeRaw = random_bytes(32);
        $challengeBase64 = rtrim(strtr($this->userEntityHelper->base64UrlEncode($challengeRaw), "+/", "-_"), "=");
        $this->storeChallenge($challengeBase64);
        Yii::$app->session->set('allow_credentials', $credentialDescriptors);

        return [
            'success' => true,
            'challenge' => $challengeBase64,
            'rpId' => Yii::$app->request->hostName,
            'allowCredentials' => $credentialDescriptors
        ];
    }

    public function challenge($credentialId): array
    {
        if ($credentialId === false) {
            return ['success' => false, 'message' => 'Invalid credential ID format'];
        }

        $credentialIdB64url = rtrim(strtr($this->userEntityHelper->base64UrlEncode($credentialId), '+/', '-_'), '=');

        $passkey = UserEntity::find()->where(['credential_id' => $credentialIdB64url])->one();
        if (!$passkey) {
            Yii::error('Passkey not found for credential ID: ' . bin2hex($credentialId), __METHOD__);
            return ['success' => false, 'message' => 'Passkey not found'];
        }

        $user = User::findOne($passkey->user_id);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found'];
        }

        $userEntity = PublicKeyCredentialUserEntity::create($user->username, (string)$user->id, $user->username); //in the table user of usuario we don't have the real name of a account so we create PublicKeyCredentialUserEntity using the username two times
        $challengeBase64 = $this->retrieveChallenge();

        return [
            $this->userEntityHelper->base64UrlDecode($challengeBase64),
            $userEntity,
            $passkey,
        ];
    }

    public function actionCreatePasskey(){
        $model = new UserEntity();
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdatePasskey($id)
    {
        $model = UserEntity::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Passkey not found.");
        }

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate() && $model->save()) {
                \Yii::$app->session->setFlash('success', 'Passkey updated successfully.');
                return $this->redirect(['index-passkey']);
            } else {
                $errors = $model->getFirstErrors();
                $errorMessage = reset($errors) ?: 'Unable to save changes.';
                \Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDeletePasskey($id)
    {
        $model = UserEntity::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException("Passkey not found.");
        }

        try {
            if ($model->delete() !== false) {
                \Yii::$app->session->setFlash('success', 'Passkey deleted successfully.');
            } else {
                \Yii::$app->session->setFlash('error', 'Unable to delete the passkey.');
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', 'Error occurred while deleting: ' . $e->getMessage());
        }

        return $this->redirect(['index-passkey']);
    }

    public function loadTableData(){
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \Da\User\Model\UserEntity::find()->where(['user_id' => Yii::$app->user->id]),
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ]
            ],
        ]);
        return $dataProvider;
    }


    public function deleteExpiredPasskeys(){
        /** @var Module $module */

        $module = Yii::$app->getModule('user');
        $data = $module->maxPasskeyAge;
        $module = Yii::$app->getModule('user');
        $userId = Yii::$app->user->id;
        $maxMonths = $module->maxPasskeyAge ?? 12;
        $models = UserEntity::find();
        return $this->asJson(['success' => true]);
    }

    public function actionIndexPasskey()
    {
        $dataProvider = $this->loadTableData();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPopupPasskey()
    {
        return $this->render('popup-passkey');
    }

    public function actionStorePasskey()
    {

        $model = new UserEntity();

        if ($model->load(Yii::$app->request->post())) {
            $user = Yii::$app->user->identity;
            $model->id = (int)$model->id;
            $model->user_id = $user->id;
            $model->type = 'public-key';
            $model->created_at = date('Y-m-d H:i:s');
            $model->credential_id = rtrim(strtr($model->credential_id, '+/', '-_'), '=');

            if (isset($model->attestation_format)) {
                if (is_array($model->attestation_format)) {
                    $formats = array_map('trim', $model->attestation_format);
                    $model->attestation_format = implode(',', $formats);
                } else {
                    $formats = array_map('trim', explode(',', $model->attestation_format));
                    $model->attestation_format = implode(',', $formats);
                }
            } else {
                $model->attestation_format = null;
            }

            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Passkey registered succesfully.');
                $dataProvider = $this->loadTableData();
                return $this->render('index', [
                    'dataProvider' => $dataProvider,
                ]);
            }

            Yii::error('Error while saving the passkey: ' . json_encode($model->getErrors()));
            Yii::$app->session->setFlash('error', Html::errorSummary($model, [
                'header' => 'Validation error: ',
            ]));
        }

        return $this->render('create', ['model' => $model]);
    }


    public function actionLoginPasskey()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $body = Json::decode($request->rawBody);

            $credentialIdB64 = ArrayHelper::getValue($body, 'id');
            $response = ArrayHelper::getValue($body, 'response', []);

            // if id is missing -> return to the challenge
            if (!$credentialIdB64) {
                return $this->asJson($this->userEntityHelper->utf8ize($this->challengeGeneration())); //generation of the challegne it returns an encoded array in Base64
            }

            // verify the response
            if (
                empty($response['clientDataJSON']) ||
                empty($response['authenticatorData']) ||
                empty($response['signature'])
            ) {
                return $this->asJson(['success' => false, 'message' => 'Incomplete WebAuthn response']);
            }


            $credentialId = $this->userEntityHelper->base64UrlDecode($credentialIdB64);
            [$challenge, $userEntity, $passkey] = $this->challenge($credentialId);
            /*challenge for the user, it verifies that the user exists, retrive the challenge and it returns
                    self::base64UrlDecode($challengeBase64), decoded challenge
                    $userEntity, the UserEntity tha have
                    $passkey,

            */


            if (empty($challenge)) {
                return $this->asJson(['success' => false, 'message' => 'Challenge non valida']);
            } else {
                $challengeBase64 = $this->userEntityHelper->base64UrlEncode($challenge);
            }


            //clientDataJson and clientDataArray contains the same things but JSON is not formatted

            $clientDataJSON = $this->userEntityHelper->base64UrlDecode($response['clientDataJSON']); //inside here we have the client challenge and the origin
            $clientDataArray = Json::decode($clientDataJSON);
            $authenticatorDataBytes = $this->userEntityHelper->base64UrlDecode($response['authenticatorData']);


            //we compare the client challenge with the one that we generated
            if (($clientDataArray['challenge'] ?? '') !== $challengeBase64) {
                $this->storeChallenge(null);
                return $this->asJson(['success' => false, 'message' => 'Challenge mismatch']);
            }

            $originNotFormatted= $clientDataArray['origin'];
            $parsedUrl = parse_url($originNotFormatted);
            $rpId = $parsedUrl['host'];

            $expectedRpIdHash = hash('sha256', $rpId, true);
            $rpIdHash = substr($authenticatorDataBytes, 0, 32);  //this must match the first 32 byte of authenticatorDataBytes
            $flags = $authenticatorDataBytes[32]; //state of the authenticator device embedded at the 33th byte of authenticatorDataBytes

            if ($rpIdHash !== $expectedRpIdHash) {
                throw new \RuntimeException('rpId hash mismatch!');
            }

            $authenticatorData = new AuthenticatorData(
                $authenticatorDataBytes,
                $rpIdHash,
                $flags,
                unpack('N', substr($authenticatorDataBytes, 33, 4))[1],
                null,
                null
            );

            $collectedClientData = new CollectedClientData(
                $clientDataJSON, //raw
                $clientDataArray); //formatted

            //finding the user using his credential id
            $model = UserEntity::findOne(['credential_id' => $credentialIdB64]);
            $userHandle = (string) $model->user_id; //and then the FK for the table user of usuario

            $assertionResponse = new AuthenticatorAssertionResponse(
                $collectedClientData,
                $authenticatorData,
                $this->userEntityHelper->base64UrlDecode($response['signature']),
                $userHandle,
            );

            $validator = $this->createAssertionValidator(); //validation of the address (only in development)
            $repository = new MyPublicKeyCredentialSourceRepository();

            try {
                $publicKeyCredentialSource = $repository->findOneByCredentialId($this->userEntityHelper->utf8ize($credentialIdB64)); //must use base64 format, this must match the credential_id row
                if ($publicKeyCredentialSource === null) {
                    return $this->asJson([
                        'success' => false,
                        'message' => 'Credential not found'
                    ]);
                }

                $requestOptions = new PublicKeyCredentialRequestOptions(
                    $this->userEntityHelper->base64UrlDecode($challengeBase64),
                    $rpId,
                    [],
                    'preferred',
                    100,
                    null,
                );

                $publicKeyCredentialSource = $validator->check(
                    $publicKeyCredentialSource,
                    $assertionResponse,
                    $requestOptions,
                    Yii::$app->request->hostName,
                    $userHandle
                );

                $userHandle = $publicKeyCredentialSource->userHandle;
                $user = User::findOne($userHandle);
                if (!$user) {
                    Yii::error('User not found for handle: ' . $publicKeyCredentialSource->userHandle, __METHOD__);
                    return $this->asJson([
                        'success' => false,
                        'message' => 'User not found'
                    ]);
                }

                Yii::$app->user->login($user);

                $passkey->sign_count = $passkey->sign_count+1;
                $passkey->last_used_at = date('Y-m-d H:i:s');
                $passkey->save(false, ['sign_count', 'last_used_at']);
                return $this->asJson(['success' => true]);

            } catch (\Throwable $e) {
                Yii::error('Login passkey error: ' . $e->getMessage(), __METHOD__);
                return $this->asJson([
                    'success' => false,
                    'message' => 'Verification error of WebAuthn',
                    'error' => $e->getMessage()
                ]);
            }
        }
        return $this->render('login-passkey');
    }

    //this function is fundamental because while developing (unless you're working on an https application) you must validate your server
    //adress to be trusted by the web-authn library
    function createAssertionValidator(): AuthenticatorAssertionResponseValidator
    {
        $ceremonyStepManager = new CeremonyStepManager([
            new CheckAllowedOrigins([Yii::$app->request->hostName], false),
        ]);
        return new AuthenticatorAssertionResponseValidator($ceremonyStepManager);
    }

    //this function puts the challenge as a session variable
    private function storeChallenge(?string $challengeBase64): void
    {
        Yii::$app->session->set('webauthn_challenge', $challengeBase64);
    }

    //this function is for retrive the challenge saved in the session
    protected function retrieveChallenge(): ?string
    {
        return Yii::$app->session->get('webauthn_challenge') ?: null;
    }



}
