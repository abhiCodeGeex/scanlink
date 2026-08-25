{{--
    Global flatpickr loader + the slFsLogDatePicker Alpine component.

    Registered as a panel render hook (NOT per-page) because the panels run in SPA mode
    (->spa()): Livewire navigation swaps the page WITHOUT executing that page's <script>
    tags, so assets/components defined inside a page view only exist after a hard refresh.
    Here they load once on the first full page load and survive every SPA navigation.
    The component polls for flatpickr in case init ever runs before the script parses.
--}}
<link rel="stylesheet" href="{{ asset('vendor/flatpickr/flatpickr.min.css') }}">
<script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
<script>
    window.slFsLogDatePicker = function (config) {
        return {
            fp: null,
            init() {
                var self = this;
                var tries = 0;

                (function boot() {
                    if (! self.$refs.input || ! self.$refs.input.isConnected) {
                        return;
                    }
                    if (typeof flatpickr !== 'function') {
                        if (++tries < 100) { setTimeout(boot, 50); }
                        return;
                    }
                    if (self.fp) {
                        return;
                    }
                    self.fp = flatpickr(self.$refs.input, {
                        dateFormat: 'd/m/Y',
                        allowInput: true,
                        disableMobile: true,
                        defaultDate: config.initial || null,
                        onChange: function (dates, str) { self.$wire.set(config.property, str || ''); },
                        onClose: function (dates, str) { self.$wire.set(config.property, str || ''); },
                    });
                })();
            },
            destroy() {
                if (this.fp) { this.fp.destroy(); this.fp = null; }
            },
        };
    };
</script>
<style>
    .flatpickr-calendar { z-index: 100001 !important; font-family: Arial, Helvetica, sans-serif !important; }
    .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #008901 !important; border-color: #008901 !important; }
    .flatpickr-day.today { border-color: #008901 !important; }
    .flatpickr-day:hover { background: #e8f6e8 !important; border-color: #e8f6e8 !important; }
</style>
