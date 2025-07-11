<?php

namespace Da\User\Repository;

use Da\User\Controller\UserEntityController;
use Da\User\Helper\UserEntityHelper;
use Da\User\Model\UserEntity;
use Webauthn\AttestationStatement\AttestationObjectLoader;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorData;
use Webauthn\Denormalizer\TrustPathDenormalizer;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\AttestedCredentialData;
use Webauthn\TrustPath\EmptyTrustPath;
use Webauthn\TrustPath\TrustPath;
use Symfony\Component\Uid\Uuid;



class MyPublicKeyCredentialSourceRepository
{
    public $userEntityHelper;

    public function __construct()
    {
        $this->userEntityHelper = new UserEntityHelper();
    }
    public function findOneByCredentialId(string $credentialIdB64): ?PublicKeyCredentialSource
    {
        $model = UserEntity::findOne(['credential_id' => $credentialIdB64]);
        if (!$model) {
            return null;
        }

        [$aaguid, $trustPath] = $this->extractAaguidAndTrustPath($model->public_key);

        return new PublicKeyCredentialSource(
            $model->credential_id,
            'public-key',
            [], // transports
            $model->attestation_format ?? 'none',
            $trustPath,
            $aaguid,
            $model->public_key,
            (string) $model->user_id,
            (int) $model->sign_count,
            null,
            null,
            null,
            null
        );
    }

    public function extractAaguidAndTrustPath(string $attestationObjectBase64url): array
    {
        $attestationStatementSupportManager = AttestationStatementSupportManager::create();
        $attestationObjectLoader = new AttestationObjectLoader($attestationStatementSupportManager);

        try {
            $attestationObject = $attestationObjectLoader->load($attestationObjectBase64url);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Errore nel parsing dell\'attestationObject: ' . $e->getMessage());
        }

        $authenticatorData = $attestationObject->authData;

        if (!$authenticatorData instanceof AuthenticatorData) {
            throw new \RuntimeException('AuthenticatorData non valida');
        }

        $attestedCredentialData = $authenticatorData->attestedCredentialData;

        if (!$attestedCredentialData instanceof AttestedCredentialData) {
            throw new \RuntimeException('AttestedCredentialData mancante o non valida');
        }

        $aaguid = $attestedCredentialData->aaguid;
        if (!$aaguid instanceof Uuid) {
            throw new \RuntimeException('AAGUID non valido');
        }

        $trustPathRaw = $attestationObject->attStmt->trustPath ?? 'none';

        //if doesn't exixst it creates an emptyTrustPath object taht is 'none'
        if ($trustPathRaw === null) {
            $trustPath = new \Webauthn\TrustPath\EmptyTrustPath();
        } elseif (is_array($trustPathRaw)) {
            $denormalizer = new TrustPathDenormalizer();
            $trustPath = $denormalizer->denormalize($trustPathRaw, \Webauthn\TrustPath\TrustPath::class);
        } elseif ($trustPathRaw instanceof \Webauthn\TrustPath\TrustPath) {
            //if it's alredy an object it is used directly
            $trustPath = $trustPathRaw;
        } else {
            throw new \RuntimeException('TrustPath in formato non riconosciuto');
        }
        if (!$trustPath instanceof \Webauthn\TrustPath\TrustPath) {
            throw new \RuntimeException('TrustPath non valido');
        }

        return [$aaguid, $trustPath];
    }







    public function findAllForUserEntity($userEntity): array
    {
        $sources = [];
        $models = UserEntity::find()->where(['user_id' => $userEntity])->all();

        var_dump();die();
        foreach ($models as $model) {
            $sources[] = new PublicKeyCredentialSource(
                $model->credential_id,
                (string) $model->user_id,
                $model->type ?? PublicKeyCredentialSource::TYPE_PUBLIC_KEY,
                [],
                $model->attestation_format ?? 'none',
                $model->public_key,
                (int) $model->sign_count
            );
        }

        return $sources;
    }
}
