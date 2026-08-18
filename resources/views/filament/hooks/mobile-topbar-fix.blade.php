{{--
    Topbar fixes shipped INLINE (not via the external scanlink-theme.css / portal-dark.css)
    so they are immune to the versioned-CSS browser cache: when a CSS file changes but a
    phone still holds the old ?v= copy, the header stays broken. Inline styles re-render on
    every authenticated page load, so these always apply.

    Covers, in both light and dark themes and every width:
      1. Hamburger icon colour — white, so it reads on the green (light) / dark banner.
      2. Dark-mode logo — a stale rule hid the topbar logo; show it again on its white pill.
      3. Mobile (<lg): hide the duplicate desktop-collapse hamburger; stop the fixed-width
         global search overlapping the logo.
--}}
<style>
    /* 1. Hamburger + mobile close icon: the topbar banner is green in light mode and
          near-black in dark mode, so a white icon reads on both. Filament's default is a
          dark grey that disappears on the green banner. */
    .fi-topbar .fi-topbar-open-sidebar-btn,
    .fi-topbar .fi-topbar-open-sidebar-btn svg,
    .fi-topbar .fi-topbar-close-sidebar-btn,
    .fi-topbar .fi-topbar-close-sidebar-btn svg {
        color: #ffffff !important;
    }

    /* 2. Dark mode: the topbar logo was hidden by a stale rule (it assumed the sidebar
          header still carried a logo, but that header is now hidden), leaving NO logo at
          all. Show the logo again — its white pill keeps it readable on the dark banner. */
    html.dark .fi-topbar img.fi-logo.fi-logo-dark,
    html.dark .fi-panel-portal .fi-topbar img.fi-logo.fi-logo-dark,
    html.dark .fi-topbar a.fi-logo,
    html.dark .fi-panel-portal .fi-topbar a.fi-logo,
    html.dark .fi-topbar .fi-logo-ctn,
    html.dark .fi-panel-portal .fi-topbar .fi-logo-ctn {
        display: revert !important;
    }
    /* Keep the light variant hidden in dark mode (Filament shows exactly one). */
    html.dark .fi-topbar img.fi-logo.fi-logo-light {
        display: none !important;
    }

    /* 3a. Below Filament's `lg` sidebar breakpoint the sidebar becomes an overlay with its
           own hamburger, so the relocated desktop *collapse* button shows as a SECOND
           hamburger. Hide it — only the overlay hamburger should remain. */
    @media (max-width: 1023.98px) {
        .fi-topbar .fi-topbar-collapse-sidebar-btn-ctn { display: none !important; }
        .fi-topbar { padding-inline: 0.75rem !important; }
        .fi-topbar .fi-global-search-ctn,
        .fi-topbar [data-global-search-input] {
            flex: 1 1 auto !important;
            min-width: 0 !important;
            max-width: none !important;
        }
    }

    /* 3b. Phones: hide the topbar search entirely (nav lives in the sidebar) so the logo,
           hamburger, bell and avatar always fit on one clean row. */
    @media (max-width: 640px) {
        .fi-topbar .fi-global-search-ctn,
        .fi-topbar [data-global-search-input] { display: none !important; }
        .fi-topbar { min-height: 3.5rem !important; padding-inline: 0.6rem !important; }
        .fi-logo { max-width: 8.5rem !important; height: 2.25rem !important; }
        .fi-topbar-start { gap: 0.4rem; }
        .fi-topbar-end { gap: 0.35rem; }
    }

    /* 4. Profile editor/create — the two-column (500px + 450px) layout must collapse to a
          single full-width column on tablets/phones. Shipped INLINE (not only in the cached
          scanlink-theme.css) so a stale cached stylesheet on a phone can't leave the editor
          in its wide desktop layout with the right column clipped off-screen. */
    @media (max-width: 1100px) {
        .sl-profile-editor,
        .sl-profile-editor--survey,
        .sl-profile-editor--code,
        .sl-profile-editor--voc,
        .sl-profile-editor--exhibit,
        .sl-profile-editor--misc,
        .sl-profile-editor--asset {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            grid-template-columns: none !important;
            grid-template-areas: none !important;
        }
        .sl-profile-editor .add-form-left,
        .sl-profile-editor .sl-add-form-left,
        .sl-profile-editor .sl-add-form-left--survey,
        .sl-profile-editor .add-form-right,
        .sl-profile-editor .sl-add-form-right {
            float: none !important;
            width: 100% !important;
            max-width: 100% !important;
            grid-area: auto !important;
        }
        .sl-survey-form-builder,
        .sl-profile-editor--survey .sl-survey-form-builder { max-width: 100% !important; }
    }

    /* 5. Global mobile safety net: no portal page should scroll sideways, and long content
          (inputs, the QR/preview column, the iPhone mock, tables) must never push past the
          screen. Fluid, not clipped. */
    @media (max-width: 1023.98px) {
        .fi-main-ctn, .fi-main, .fi-page, .fi-page-content { max-width: 100% !important; min-width: 0 !important; }
        .fi-main { overflow-x: clip; }
        .sl-profile-editor img,
        .sl-profile-editor iframe,
        .sl-profile-editor .iphone-preview-container { max-width: 100% !important; }
        .fi-input-wrp, .fi-fo-field-wrp, .fi-fo-component-ctn { min-width: 0 !important; max-width: 100% !important; }
    }
</style>
