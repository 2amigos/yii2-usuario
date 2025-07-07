function arrayBufferToBase64url(buffer) {
    return btoa(String.fromCharCode(...new Uint8Array(buffer)))
        .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
}

function generateUUIDv4() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = crypto.getRandomValues(new Uint8Array(1))[0] & 15;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const startButton = document.getElementById('start-passkey-btn');

    if (!startButton) return;

    startButton.addEventListener('click', async function () {
        try {
            document.getElementById('uuid_id').value = generateUUIDv4();

            let challenge = new Uint8Array(32);
            window.crypto.getRandomValues(challenge);

            const publicKey = {
                challenge: challenge.buffer,
                rp: { name: "TEST - Passkeys with Yii2-Usuario" },
                user: {
                    id: new TextEncoder().encode(userId),
                    name: username,
                    displayName: username
                },
                pubKeyCredParams: [
                    { type: "public-key", alg: -7 },
                    { type: "public-key", alg: -257 }
                ],
                authenticatorSelection: {
                    userVerification: "preferred"
                },
                timeout: 60000,
                attestation: "direct"
            };

            const credential = await navigator.credentials.create({ publicKey });

            if (!credential || !credential.response) {
                throw new Error("Credential non valida.");
            }

            document.getElementById('credential_id').value = arrayBufferToBase64url(credential.rawId);
            document.getElementById('public_key').value = arrayBufferToBase64url(credential.response.attestationObject);
            document.getElementById('sign_count').value = 0;
            document.getElementById('attestation_format').value = 'direct';
            document.getElementById('device_id').value = navigator.userAgent;

            document.getElementById('submit-button').click();
        } catch (error) {
            alert('Errore durante la registrazione della passkey: ' + error.message);
        }
    });
});
