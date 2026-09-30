@props([
    'recaptcha',
    'formId',
    'livewireId' => null,
    'method' => 'submit',
])
@php
    $fn = 'slRecaptcha_'.preg_replace('/[^A-Za-z0-9_]/', '_', $formId);
@endphp

<script src="https://www.google.com/recaptcha/api.js?render={{ $recaptcha->siteKey() }}" async defer></script>
<script>
    (function () {
        var form = document.getElementById(@js($formId));

        if (! form || form.dataset.slRecaptcha) { return; }

        form.dataset.slRecaptcha = '1';

        var sending = false;

        // A v3 token is only good for a couple of minutes, so it is fetched as the form is
        // sent rather than when the page loads - otherwise anyone who reads the page before
        // writing their message would be turned away for a token that had gone stale.
        form.addEventListener('submit', function (event) {
            if (sending) { return; }

            event.preventDefault();

            if (typeof grecaptcha === 'undefined') {
                // The script did not load. Send it anyway: the server decides, and a blocked
                // CDN should not be able to silence the contact form.
                sending = true;
                form.submit();

                return;
            }

            grecaptcha.ready(function () {
                grecaptcha
                    .execute(@js($recaptcha->siteKey()), { action: @js($recaptcha->action()) })
                    .then(function (token) {
                        @if ($livewireId)
                            // Handed straight to the component rather than through a bound
                            // property: the token has to be the one this very submission got.
                            window.Livewire.find(@js($livewireId)).call(@js($method), token);
                        @else
                            var field = form.querySelector('input[name="g-recaptcha-response"]');

                            if (field) { field.value = token; }

                            sending = true;
                            form.submit();
                        @endif
                    })
                    .catch(function () {
                        @if ($livewireId)
                            window.Livewire.find(@js($livewireId)).call(@js($method), '');
                        @else
                            sending = true;
                            form.submit();
                        @endif
                    });
            });
        });
    })();
</script>
