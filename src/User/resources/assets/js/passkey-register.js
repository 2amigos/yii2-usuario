jQuery(function ($) {
    $.fn.bindPassKeyCreationSubmit = function () {
        // We expect the function to be called on a yii form
        const form = $(this)
        form.on('beforeSubmit', async function (e) {

            switch (form.data('creating-credentials')) {
                case undefined:
                    e.preventDefault()
                    form.data('creating-credentials', true)
                    await form.registerWithPasskey()
                    return false
                case true:
                    e.preventDefault()
                    return false
                case false:
                    return true
            }
        }).submit(function (e) {
            const prevent = form.data('creating-credentials')
            if (prevent === true || prevent === undefined) {
                e.preventDefault();
                return false;
            }
        })
    }

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
            //generate a casual challenge and when it's ready it's send to the browser.
            window.crypto.getRandomValues(challenge);


            const publicKey = {
                challenge: challenge.buffer,
                rp: {
                    id: window.location.hostname,
                    name: "My passkey "+numberOfPasskeys },
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
                throw new Error("Credential invalid.");
            }


            $('#credential_id').val(arrayBufferToBase64url(credential.rawId));
            $('#public_key').val(arrayBufferToBase64url(credential.response.attestationObject));
            $('#attestation_format').val('direct');
            $('#device_id').val(navigator.userAgent);
            $(this).data('creating-credentials', false).submit();

            return true;
        } catch (err) {
            alert('There was an error during the registration of the passkey: ' + err.message);
            $(this).data('creating-credentials', undefined);
            return false;
        }

    };
});
