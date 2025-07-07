(jQuery(function($) {
    $.fn.loginWithPasskey = async function() {

        const href = $(this).attr('href')
        const csrfToken = yii.getCsrfToken();

        function base64UrlEncode(buffer) {
            let binary = '';
            const bytes = new Uint8Array(buffer);
            for (let i = 0; i < bytes.byteLength; i++) {
                binary += String.fromCharCode(bytes[i]);
            }
            return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '');
        }

        function base64UrlToUint8Array(base64UrlString) {
            let base64 = base64UrlString.replace(/-/g, '+').replace(/_/g, '/');
            while (base64.length % 4) {
                base64 += '=';
            }
            const binaryString = atob(base64);
            const bytes = new Uint8Array(binaryString.length);
            for (let i = 0; i < binaryString.length; i++) {
                bytes[i] = binaryString.charCodeAt(i);
            }
            return bytes;
        }

        try {
            const resInit = await fetch(href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrfToken
                        },
                        body: JSON.stringify({})
                    }
                );
            const initData = await resInit.json();
            if (!initData.success) {
                alert('Errore: ' + (initData.message || 'Dati iniziali non validi'));
                return;
            }
            const publicKey = {
                challenge: base64UrlToUint8Array(initData.challenge),
                timeout: 60000,
                rpId: initData.rpId,
                allowCredentials: initData.allowCredentials.map(cred => ({
                    id: base64UrlToUint8Array(cred.id),
                    type: cred.type,
                    transports: cred.transports
                })),
                userVerification: 'preferred'
            };

            const assertion = await navigator.credentials.get({publicKey});

            const response = {
                id: assertion.id,
                type: assertion.type,
                rawId: base64UrlEncode(assertion.rawId),
                response: {
                    clientDataJSON: base64UrlEncode(assertion.response.clientDataJSON),
                    authenticatorData: base64UrlEncode(assertion.response.authenticatorData),
                    signature: base64UrlEncode(assertion.response.signature),
                    userHandle: assertion.response.userHandle ? base64UrlEncode(assertion.response.userHandle) : null
                }
            };
            const res = await fetch(href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-Token': csrfToken
                        },
                        body: JSON.stringify(response)
                    }
                );

            let result;
            try {
                result = await res.json();
            } catch (e) {
                const text = await res.text();
                alert('Errore nel server: risposta non valida');
                return;
            }

            if (result.success) {
                window.location.reload();
            } else {
                alert('Autenticazione fallita: ' + (result.message || 'Errore sconosciuto'));
            }
        } catch (err) {
            alert("Errore durante l'autenticazione con passkey.");
            console.error(err);
        }
    }
}));
