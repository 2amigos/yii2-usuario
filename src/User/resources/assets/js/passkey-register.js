jQuery(function ($) {
    $.fn.registerWithPasskey = async function () {

        function arrayBufferToBase64url(buffer) {
            return btoa(String.fromCharCode(...new Uint8Array(buffer)))
                .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }

        function generateUUIDv4() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
                const r = crypto.getRandomValues(new Uint8Array(1))[0] & 15;
                const v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }
        try {
            $('#uuid_id').val(generateUUIDv4());
            const challenge = new Uint8Array(32);
            window.crypto.getRandomValues(challenge);

            const publicKey = {
                challenge: challenge.buffer,
                rp: { name: "My passkey "+numberOfPasskeys },
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

            if (!credential?.response) {
                throw new Error("Credential non valida.");
            }

            $('#credential_id').val(arrayBufferToBase64url(credential.rawId));
            $('#public_key').val(arrayBufferToBase64url(credential.response.attestationObject));
            $('#sign_count').val(0);
            $('#attestation_format').val('direct');
            $('#device_id').val(navigator.userAgent);
            $('#submit-button').click();
        } catch (err) {
            alert('Errore durante la registrazione della passkey: ' + err.message);
        }

    };
});
