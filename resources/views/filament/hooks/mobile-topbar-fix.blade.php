{{--
    Mobile topbar fix, shipped INLINE (not via the external scanlink-theme.css) so it is
    immune to the versioned-CSS browser cache: when the CSS file changes but a phone still
    holds the old ?v= copy, the header stays broken. Inline styles re-render with every
    authenticated page load, so this always applies.

    Below Filament's `lg` sidebar breakpoint the sidebar becomes an overlay with its own
    hamburger, so the relocated desktop *collapse* button (which the theme forces visible on
    desktop) shows as a SECOND hamburger. Hide it on mobile — only the overlay hamburger
    should remain. Also stop the fixed-width global search from overlapping the logo.
--}}
<style>
    @media (max-width: 1023.98px) {
        /* Kill the duplicate (desktop collapse) hamburger — leave only the overlay one. */
        .fi-topbar .fi-topbar-collapse-sidebar-btn-ctn { display: none !important; }
        .fi-topbar { padding-inline: 0.75rem !important; }
        /* Let the search shrink into the space between logo and actions (no overlap). */
        .fi-topbar .fi-global-search-ctn,
        .fi-topbar [data-global-search-input] {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            max-width: none !important;
        }
    }

    @media (max-width: 640px) {
        /* Phones: hide the topbar search entirely (nav lives in the sidebar) so the logo,
           hamburger, bell and avatar always fit on one clean row. */
        .fi-topbar .fi-global-search-ctn,
        .fi-topbar [data-global-search-input] { display: none !important; }
        .fi-topbar { min-height: 3.5rem !important; padding-inline: 0.6rem !important; }
        .fi-logo { max-width: 8.5rem !important; height: 2.25rem !important; }
        .fi-topbar-start { gap: 0.4rem; }
        .fi-topbar-end { gap: 0.35rem; }
    }
</style>
