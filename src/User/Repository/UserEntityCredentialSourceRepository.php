<?php

namespace Da\User\Repository;

use Da\User\Helper\UserEntityHelper;
use Da\User\Model\UserEntity;
use Webauthn\AttestationStatement\AttestationObjectLoader;
use Webauthn\AttestationStatement\AttestationStatementSupportManager;
use Webauthn\AuthenticatorData;
use Webauthn\Denormalizer\TrustPathDenormalizer;
use Webauthn\PublicKeyCredentialSource;
use Webauthn\AttestedCredentialData;
use Symfony\Component\Uid\Uuid;
use Webauthn\AttestationStatement\PackedAttestationStatementSupport;
use Cose\Algorithm;
use Webauthn\AttestationStatement\NoneAttestationStatementSupport;
use Webauthn\AttestationStatement\AndroidKeyAttestationStatementSupport;
use Webauthn\AttestationStatement\TPMAttestationStatementSupport;

class UserEntityCredentialSourceRepository
{
    public $userEntityHelper;

    public function __construct()
    {
        $this->userEntityHelper = new UserEntityHelper();
    }

    public function findOneByCredentialId(string $credentialIdB64): ?PublicKeyCredentialSource
    {
        //find the user by credential_id
        $model = UserEntity::findOne(['credential_id' => $credentialIdB64]);
        if (!$model) {
            return null;
        }
        [$aaguid, $trustPath] = $this->extractAaguidAndTrustPath($model->public_key);

        //it returns the PublicKeyCredentialSource with all the data ready to be checked for the login
        return new PublicKeyCredentialSource(
            $model->credential_id,
            'public-key',
            [], // transports
            $model->attestation_format ?? 'none',
            $trustPath,
            $aaguid,
            $model->public_key,
            (string)$model->user_id,
            (int)$model->sign_count,
            null,
            null,
            null,
            null
        );
    }

    //this function extract the aaguid and the trust path from the attestation object.
    //also this function allows some other types of passkey login
    public function extractAaguidAndTrustPath(string $attestationObjectBase64url): array
    {
        $attestationStatementSupportManager = AttestationStatementSupportManager::create();
        $algorithmManager = new Algorithm\Manager();

        //types of passkey allowed
        $attestationStatementSupportManager->add(new NoneAttestationStatementSupport());
        $attestationStatementSupportManager->add(new PackedAttestationStatementSupport($algorithmManager));
        $attestationStatementSupportManager->add(new AndroidKeyAttestationStatementSupport());
        $attestationStatementSupportManager->add(new TPMAttestationStatementSupport());
        //creation of the obj for accessing to the attestation obj
        $attestationObjectLoader = new AttestationObjectLoader($attestationStatementSupportManager);
        //load of the attestation obj
        try {
            $attestationObject = $attestationObjectLoader->load($attestationObjectBase64url);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error during the parsing of attestationObject: ' . $e->getMessage());
        }
        //in the next step we are going to extract some data from the previous step like in the next line of code, this process is done
        //for all the variables after this comment till the rawTrustPath
        $authenticatorData = $attestationObject->authData;
        if (!$authenticatorData instanceof AuthenticatorData) {
            throw new \RuntimeException('AuthenticatorData not valid');
        }

        $attestedCredentialData = $authenticatorData->attestedCredentialData;
        if (!$attestedCredentialData instanceof AttestedCredentialData) {
            throw new \RuntimeException('AttestedCredentialData missing or invalid');
        }

        $aaguid = $attestedCredentialData->aaguid;
        if (!$aaguid instanceof Uuid) {
            throw new \RuntimeException('AAGUID invalid');
        }

        $trustPathRaw = $attestationObject->attStmt->trustPath ?? 'none';

        //if doesn't exixst it creates an emptyTrustPath object that is 'none'
        //there are two types of trusthpaths that are different, if the trustpath is empty the type will be of an EmptyTrustPath otherwise
        //it'll be a TrustPath
        if ($trustPathRaw === null) {
            $trustPath = new \Webauthn\TrustPath\EmptyTrustPath();
        } elseif (is_array($trustPathRaw)) {
            $denormalizer = new TrustPathDenormalizer();
            $trustPath = $denormalizer->denormalize($trustPathRaw, \Webauthn\TrustPath\TrustPath::class);
        } elseif ($trustPathRaw instanceof \Webauthn\TrustPath\TrustPath) {
            //if it's alredy an object it is used directly
            $trustPath = $trustPathRaw;
        } else {
            throw new \RuntimeException('TrustPath is in an unknown format');
        }
        if (!$trustPath instanceof \Webauthn\TrustPath\TrustPath) {
            throw new \RuntimeException('TrustPath invalid');
        }

        return [$aaguid, $trustPath];
    }
}
