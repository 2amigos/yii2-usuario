<?php

namespace Da\User\Controller;

use Da\User\Helper\UserEntityHelper;
use Random\RandomException;
use Webauthn\CeremonyStep\CeremonyStepManager;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\CeremonyStep\CheckAllowedOrigins;
use Da\User\Model\UserEntity;
use Da\User\Model\User;
use Da\User\Repository\UserEntityCredentialSourceRepository;
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
use \Da\User\Traits\ModuleAwareTrait;


class UserEntityController extends Controller
{
    use ModuleAwareTrait;
    public $enableCsrfValidation = true;
    public UserEntityHelper $userEntityHelper;


    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->userEntityHelper = new UserEntityHelper();
    }

    /**
     * @throws RandomException
     */
    public function challengeGeneration(): array
    {
        $passkeys = UserEntity::find()->Andwhere(['type' => 'public-key'])->all();

        if (empty($passkeys)) {
            return ['success' => false, 'message' => Yii::t('usuario', 'No passkey registered.')];
        }

        $credentialDescriptors = array_map(fn($pk) => [
            'type' => 'public-key',
            'id' => $pk->credential_id,
        ], $passkeys);

        //effective generation of the challenge
        $challengeRaw = random_bytes(32);
        $challengeBase64 = rtrim(strtr($this->userEntityHelper->base64UrlEncode($challengeRaw), "+/", "-_"), "=");
        $this->storeChallenge($challengeBase64);
        Yii::$app->session->set('allow_credentials', $credentialDescriptors);

        return [
            'success' => true,
            'challenge' => $challengeBase64,
            'rpId' => Yii::$app->request->hostName,   //hostname of the app it will be useful later (like in the actionLoginPasskey)
            'allowCredentials' => $credentialDescriptors
        ];
    }

    public function challenge($credentialId): array
    {
        if ($credentialId === false) {
            return ['success' => false, 'message' =>  Yii::t('usuario', 'Invalid credential ID format.')];
        }
        $credentialIdB64url = rtrim(strtr($this->userEntityHelper->base64UrlEncode($credentialId), '+/', '-_'), '=');
        $passkey = UserEntity::find()->Andwhere(['credential_id' => $credentialIdB64url])->one();
        if (!$passkey) {
            Yii::error(Yii::t('usuario', 'Passkey not found for credential ID: ') . bin2hex($credentialId), __METHOD__);
            return ['success' => false, 'message' => Yii::t('usuario', 'Passkey not found.')];
        }
        $user = User::findOne($passkey->user_id);
        if (!$user) {
            return ['success' => false, 'message' => Yii::t('usuario', 'User not found.')];
        }
        $userEntity = PublicKeyCredentialUserEntity::create($user->username, (string)$user->id, $user->username); //in the table user of usuario we don't have the real name of an account so we create PublicKeyCredentialUserEntity using the username two times
        $challengeBase64 = $this->retrieveChallenge();
        return [
            $this->userEntityHelper->base64UrlDecode($challengeBase64),
            $userEntity,
            $passkey,
        ];
    }

    //this function checks if the current can access the passkey pages
    public function checkAccessConditions() : bool
    {
        $module = $this->getModule();
        if(Yii::$app->user->isGuest||!$module->enablePasskeyLogin){
            return false;
        }
        return true;
    }

    public function actionCreatePasskey(){
        $model = new UserEntity();
        if($this->checkAccessConditions()){
            return $this->render('create', ['model' => $model]);
        }
        return $this->goBack();
    }
    //function for updating passkeys, the user can only change the name of it
    public function actionUpdatePasskey($id)
    {
        $model = UserEntity::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException(Yii::t('usuario', 'Passkey not found.'));
        }

        if ($model->load(\Yii::$app->request->post())) {
            if ($model->validate() && $model->save()) {
                \Yii::$app->session->setFlash('success', Yii::t('usuario', 'Passkey updated successfully.'));
                return $this->redirect(['index-passkey']);
            } else {
                $errors = $model->getFirstErrors();
                $errorMessage = reset($errors) ?: Yii::t('usuario', 'Unable to save changes.');
                \Yii::$app->session->setFlash('error', $errorMessage);
            }
        }

        if($this->checkAccessConditions()){
            return $this->render('update', [
                'model' => $model,
            ]);
        }
        return $this->goBack();

    }

    public function actionDeletePasskey($id)
    {
        if(!$this->checkAccessConditions()){
            return $this->goBack();
        }

        $model = UserEntity::findOne($id);
        if (!$model) {
            throw new \yii\web\NotFoundHttpException( Yii::t('usuario', 'Passkey not found.'));
        }

        try {
            if ($model->delete() !== false) {
                \Yii::$app->session->setFlash('success', Yii::t('usuario', 'Passkey deleted successfully.'));
            } else {
                \Yii::$app->session->setFlash('error', Yii::t('usuario', 'Unable to delete the passkey.'));
            }
        } catch (\Exception $e) {
            \Yii::$app->session->setFlash('error', Yii::t('usuario', 'Error occurred while deleting: ') . $e->getMessage());
        }

        return $this->redirect(['index-passkey']);
    }

    public function loadTableData(){
        $dataProvider = new \yii\data\ActiveDataProvider([
            'query' => \Da\User\Model\UserEntity::find()->Andwhere(['user_id' => Yii::$app->user->id]),
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

    public function actionIndexPasskey()
    {
        if(!$this->checkAccessConditions()){
            return $this->goBack();
        }
        $dataProvider = $this->loadTableData();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
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

            try {
                $model->attestation_format = (new \Da\User\Helper\UserEntityHelper)->extractAttestationFormat($model->public_key) ?? 'unknown';
            } catch (\Throwable $e) {
                Yii::error('CBOR decode error: ' . $e->getMessage());
                $model->attestation_format = 'unknown';
            }

            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('usuario', 'Passkey registered succesfully.'));
                $dataProvider = $this->loadTableData();
                return $this->render('index', [
                    'dataProvider' => $dataProvider,
                ]);
            }

            Yii::error(Yii::t('usuario', 'Error while saving the passkey: ') . json_encode($model->getErrors()));
            Yii::$app->session->setFlash('error', Html::errorSummary($model, [
                'header' => Yii::t('usuario', 'Validation error: '),
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
                return $this->asJson($this->userEntityHelper->utf8ize($this->challengeGeneration())); //generation of the challenge it returns an encoded array in Base64
            }
            // verify the response
            if (
                empty($response['clientDataJSON']) ||
                empty($response['authenticatorData']) ||
                empty($response['signature'])
            ) {
                return $this->asJson(['success' => false, 'message' => Yii::t('usuario', 'Incomplete WebAuthn response')]);
            }

            $credentialId = $this->userEntityHelper->base64UrlDecode($credentialIdB64);
            [$challenge, $passkey] = $this->challenge($credentialId);
            /*challenge for the user, it verifies that the user exists and retrieve the challenge. it returns ->
                    self::base64UrlDecode($challengeBase64), decoded challenge
                    $passkey, */

            if (empty($challenge)) {
                return $this->asJson(['success' => false, 'message' =>  Yii::t('usuario', 'Invalid challenge')]);
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
                return $this->asJson(['success' => false, 'message' => Yii::t('usuario', 'Challenge mismatch')]);
            }

            $originNotFormatted= $clientDataArray['origin'];
            $parsedUrl = parse_url($originNotFormatted);
            $rpId = $parsedUrl['host'];

            $expectedRpIdHash = hash('sha256', $rpId, true); //sha256 is the encoding algorithm used to encode the expected address
            $rpIdHash = substr($authenticatorDataBytes, 0, 32);  //this must match the first 32 byte of authenticatorDataBytes
            $flags = $authenticatorDataBytes[32]; //state of the authenticator device embedded at the 33rd byte of authenticatorDataBytes

            if ($rpIdHash !== $expectedRpIdHash) { //we check if the expectedRpIdHash (encoded address of the webapp) is equal to the one extracted from authenicatorDataBytes
                throw new \RuntimeException(Yii::t('usuario', 'rpId hash mismatch!'));
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
            $repository = new UserEntityCredentialSourceRepository();

            try {
                $publicKeyCredentialSource = $repository->findOneByCredentialId($this->userEntityHelper->utf8ize($credentialIdB64)); //must use base64 format, this must match the credential_id row
                if ($publicKeyCredentialSource === null) {
                    return $this->asJson([
                        'success' => false,
                        'message' => Yii::t('usuario', 'Credential not found')
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
                    Yii::error(Yii::t('usuario', 'User not found for handle: ') . $publicKeyCredentialSource->userHandle, __METHOD__);
                    return $this->asJson([
                        'success' => false,
                        'message' => Yii::t('usuario', 'User not found')
                    ]);
                }
                Yii::$app->user->login($user);

                $model->sign_count += 1;
                $model->last_used_at = date('Y-m-d H:i:s');
                $model->save(false, ['sign_count', 'last_used_at']);
                return $this->asJson(['success' => true]);

            } catch (\Throwable $e) {
                Yii::error(Yii::t('usuario', 'Login passkey error: ') . $e->getMessage(), __METHOD__);
                return $this->asJson([
                    'success' => false,
                    'message' => Yii::t('usuario', 'Verification error of WebAuthn'),
                    'error' => $e->getMessage()
                ]);
            }
        }
        return $this->render('login-passkey');
    }
    //this function is fundamental because while developing (unless you're working on a https application) you must validate your server
    //address to be trusted by the web-authn library
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
    //this function is for retrieve the challenge saved in the session
    protected function retrieveChallenge(): ?string
    {
        return Yii::$app->session->get('webauthn_challenge') ?: null;
    }
}
