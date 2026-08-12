{{-- Auth pages (login / register / password reset) are LIGHT ONLY — they never
     use dark mode. Filament's inline head script adds `dark` to <html> from the
     saved theme preference on every page; this runs afterwards (HEAD_END) and
     strips it on auth routes only, before paint, WITHOUT touching localStorage so
     the authenticated portal keeps the user's dark-mode choice. --}}
<script>
    (function () {
        function isAuthPage() {
            return /\/portal\/(login|register|password)/i.test(window.location.pathname);
        }
        function forceLight() {
            if (isAuthPage()) {
                document.documentElement.classList.remove('dark');
            }
        }
        forceLight();
        document.addEventListener('livewire:navigated', forceLight);
        document.addEventListener('DOMContentLoaded', forceLight);
    })();
</script>
