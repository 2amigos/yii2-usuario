<?php

namespace Da\User\Controller;

use app\helpers\RequestConverter;
use Da\User\Model\UserEntity;
use app\repositories\MyPublicKeyCredentialSourceRepository;
use Da\User\Model\User;
use Symfony\Component\Uid\Uuid as SymfonyUuid;
use Webauthn\CollectedClientData;
use Webauthn\PublicKeyCredentialUserEntity;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredential;
use Webauthn\AuthenticatorData;
use Webauthn\TrustPath\EmptyTrustPath;
use Yii;
use Ramsey\Uuid\Uuid;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;

function utf8ize($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = utf8ize($value);
        }
    } elseif (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}

class UserEntityController extends Controller
{
    public $enableCsrfValidation = true;

    public function challengeGeneration(): array
    {
        $passkeys = UserEntity::find()->where(['type' => 'public-key'])->all();

        if (empty($passkeys)) {
            return ['success' => false, 'message' => 'Nessuna passkey registrata'];
        }

        $credentialDescriptors = array_map(fn($pk) => [
            'type' => 'public-key',
            'id' => $pk->credential_id,
        ], $passkeys);

        $challengeRaw = random_bytes(32);
        $challengeBase64 = rtrim(strtr(base64_encode($challengeRaw), "+/", "-_"), "=");
        Yii::debug('Generated challenge: ' . $challengeBase64, __METHOD__);
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

        $credentialIdB64url = rtrim(strtr(base64_encode($credentialId), '+/', '-_'), '=');

        $passkey = UserEntity::find()->where(['credential_id' => $credentialIdB64url])->one();
        if (!$passkey) {
            Yii::error('Passkey not found for credential ID: ' . bin2hex($credentialId), __METHOD__);
            return ['success' => false, 'message' => 'Passkey non trovata'];
        }

        $user = User::findOne($passkey->user_id);
        if (!$user) {
            return ['success' => false, 'message' => 'Utente non trovato'];
        }

        $userEntity = PublicKeyCredentialUserEntity::create($user->username, (string)$user->id, $user->username);
        $challengeBase64 = $this->retrieveChallenge();

        return [
            self::base64UrlDecode($challengeBase64),
            $userEntity,
            $challengeBase64,
            $passkey,
        ];
    }

    public function actionStorePasskey()
    {
        $model = new UserEntity();

        if ($model->load(Yii::$app->request->post())) {
            $user = Yii::$app->user->identity;

            $model->user_id = $user->id;
            $model->type = 'public-key';
            $model->created_at = date('Y-m-d H:i:s');
            $model->credential_id = rtrim(strtr($model->credential_id, '+/', '-_'), '=');

            // Gestione attestation_format: se è un array o stringa, normalizza in stringa separata da virgole
            if (isset($model->attestation_format)) {
                if (is_array($model->attestation_format)) {
                    $formats = array_map('trim', $model->attestation_format);
                    $model->attestation_format = implode(',', $formats);
                } else {
                    // Pulizia base, rimuovi spazi inutili
                    $formats = array_map('trim', explode(',', $model->attestation_format));
                    $model->attestation_format = implode(',', $formats);
                }
            } else {
                // Se non è settato, salva null
                $model->attestation_format = null;
            }

            if ($model->validate() && $model->save()) {
                Yii::$app->session->setFlash('success', 'Passkey registrata con successo.');
                return $this->redirect(['index']);
            }

            Yii::error('Errore salvataggio passkey: ' . json_encode($model->getErrors()));
            Yii::$app->session->setFlash('error', 'Errore di validazione: ' . json_encode($model->getErrors()));
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionLoginPasskey()
    {
        $request = Yii::$app->request;

        if ($request->isPost) {
            $body = Json::decode($request->rawBody);

            if (!($credentialIdB64 = ArrayHelper::getValue($body, 'id'))) {
                return $this->asJson(utf8ize($this->challengeGeneration()));
            }

            $response = ArrayHelper::getValue($body, 'response', []);

            if (empty($response['clientDataJSON']) || empty($response['authenticatorData']) || empty($response['signature'])) {
                return $this->asJson(['success' => false, 'message' => 'Incomplete WebAuthn response']);
            }

            $credentialId = self::base64UrlDecode($credentialIdB64);
            [$challenge, $userEntity, $challengeBase64, $passkey] = $this->challenge($credentialId);

            if (empty($challenge)) {
                return $this->asJson(['success' => false, 'message' => 'Challenge non valida']);
            }

            $clientDataJSON = self::base64UrlDecode($response['clientDataJSON']);
            $clientDataArray = Json::decode($clientDataJSON);
            if (($clientDataArray['challenge'] ?? '') !== $challengeBase64) {
                $this->storeChallenge(null);
                return $this->asJson(['success' => false, 'message' => 'Challenge mismatch']);
            }

            $authenticatorData = new AuthenticatorData(
                self::base64UrlDecode($response['authenticatorData']),
                substr(self::base64UrlDecode($response['authenticatorData']), 0, 32),
                self::base64UrlDecode($response['authenticatorData'])[32],
                unpack('N', substr(self::base64UrlDecode($response['authenticatorData']), 33, 4))[1],
                null,
                null
            );

            $authenticatorAssertionResponse = new AuthenticatorAssertionResponse(
                new CollectedClientData($clientDataJSON, $clientDataArray),
                $authenticatorData,
                self::base64UrlDecode($response['signature']),
                $response['userHandle'] ?? null
            );

            $publicKeyCredential = new PublicKeyCredential(
                $credentialIdB64,
                PublicKeyCredentialDescriptor::CREDENTIAL_TYPE_PUBLIC_KEY,
                $credentialId,
                $authenticatorAssertionResponse
            );

            $validator = new AuthenticatorAssertionResponseValidator(
                new MyPublicKeyCredentialSourceRepository()
            );

            try {
                Yii::$app->user->login(User::findOne($passkey->user_id));
                $passkey->sign_count = $passkey->sign_count + 1;
                $passkey->last_used_at = date('Y-m-d H:i:s');
                $passkey->save(false, ['sign_count', 'last_used_at']);
                return $this->asJson(['success' => true]);
            } catch (\Throwable $e) {
                Yii::error('Login passkey error: ' . $e->getMessage(), __METHOD__);
                return $this->asJson(['success' => false, 'message' => 'Errore di verifica', 'error' => $e->getMessage()]);
            }
        }

        return $this->render('login-passkey');
    }

    private function storeChallenge(?string $challengeBase64): void
    {
        Yii::$app->session->set('webauthn_challenge', $challengeBase64);
    }

    protected function retrieveChallenge(): ?string
    {
        return Yii::$app->session->get('webauthn_challenge') ?: null;
    }

    protected static function base64UrlDecode(string $data): string
    {
        $remainder = strlen($data) % 4;
        if ($remainder) {
            $data .= str_repeat('=', 4 - $remainder);
        }
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
