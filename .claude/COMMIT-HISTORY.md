# Commit history

Every commit in this repository, oldest first — 128 of them, 2026-06-27 to 2026-09-01.

Generated because the narrative documents do not cover the whole project: `PROJECT_HISTORY.md`
is dated July 2026, and **117 of these 128 commits landed in August**. Commits whose message says
nothing useful (`fix`, `update`, …) are annotated here with the files they actually touched, so a
reader can navigate the history without opening every diff.

For the deep account of 2026-08-31 specifically, read `WORK-LOG-2026-08-31.md`.


## 2026-06 — 2 commits

- `f38dad38` 2026-06-27 — Create README.md
- `910302c9` 2026-06-30 — Initial upload

## 2026-07 — 7 commits

- `e7c2b3f7` 2026-07-16 — Replace legacy app with Laravel admin Client module
- `1553ec32` 2026-07-20 — admin client management, order management and settings modules
- `3136759c` 2026-07-24 — admin portal filter fixes
- `5c58b2f6` 2026-07-24 — client portal landing page with authentication and edit profile
- `e7a9721b` 2026-07-24 — client registration
- `706a7b90` 2026-07-31 — created frontend pages
- `7251b998` 2026-07-31 — code balance and code purchase module

## 2026-08 — 117 commits

- `1bd6975b` 2026-08-05 — missing design pages
- `b2a5583c` 2026-08-05 — design fixes
- `4a026530` 2026-08-05 — design fixes
- `7ca34ba1` 2026-08-05 — missing admin files
- `d979451c` 2026-08-05 — Send portal home to Edit Account and harden dark mode.
- `aa5f0dad` 2026-08-05 — missing admin files
- `77befbf1` 2026-08-06 — client portal qr generation
- `54918d1c` 2026-08-06 — url link missing files
- `e401956b` 2026-08-06 — url link missing files
- `4854f188` 2026-08-06 — url link missing files
- `e6d71529` 2026-08-06 — url link missing files
- `9752b7a8` 2026-08-06 — url link missing files
- `d19d6029` 2026-08-07 — url link fixes
- `38aaad71` 2026-08-07 — url link fixes
- `8a909374` 2026-08-10 — **fixes** — touched: row-tools.blade.php
- `4ba92236` 2026-08-10 — frotnend pages new design
- `dedbf398` 2026-08-10 — **fixes** — touched: HasProfileQrActions.php
- `3c740a4a` 2026-08-10 — **fixes** — touched: ViewProfile.php
- `75d6a105` 2026-08-10 — **fixes** — touched: ViewProfile.php
- `d4105f71` 2026-08-11 — landing page blurred image fix
- `e3ed1420` 2026-08-13 — all profile templates working
- `8e396a24` 2026-08-13 — migration fixes
- `205406b2` 2026-08-13 — migration fixes
- `6494d5b9` 2026-08-13 — migration fixes
- `f2631867` 2026-08-13 — migration fixes
- `29eecb11` 2026-08-14 — **fixes** — touched: AdminPanelProvider.php, ClientPortalPanelProvider.php, scanlink.php, portal-dark.css +3 more
- `dbe055f7` 2026-08-14 — **fixes** — touched: ClientPortalPanelProvider.php, navigation-feedback.blade.php
- `4ff22074` 2026-08-14 — **fixes** — touched: CreateProfile.php, EditProfile.php, PortalAuthController.php, FormBuilderDraftService.php
- `022bad63` 2026-08-18 — **fix** — touched: RefreshFromLive.php, SyncAllUsers.php, PortalAuthController.php, AdminPanelProvider.php +8 more
- `b7d9bd0a` 2026-08-18 — **fix** — touched: AdminPanelProvider.php, ClientPortalPanelProvider.php, mobile-photo-capture.blade.php
- `a581f6ab` 2026-08-18 — **fix** — touched: AdminPanelProvider.php, ClientPortalPanelProvider.php, mobile-topbar-fix.blade.php
- `7d7eb32b` 2026-08-18 — **fix** — touched: portal-dark.css, scanlink-theme.css, mobile-topbar-fix.blade.php
- `661e85b6` 2026-08-18 — **fix** — touched: ShortenProfileQrs.php, HasLegacyProfileEditorLayout.php, ProfileQrService.php
- `dba2b325` 2026-08-18 — **fix** — touched: HasLegacyProfileEditorLayout.php
- `626eaa90` 2026-08-18 — **fix** — touched: mobile-photo-capture.blade.php, show.blade.php
- `6bc35cb2` 2026-08-18 — **fix** — touched: mobile-topbar-fix.blade.php, shell.blade.php, upload-doc.blade.php, upload-image.blade.php +1 more
- `162e1af6` 2026-08-19 — **fix** — touched: MobileProfileController.php, show.blade.php
- `c24e534d` 2026-08-19 — **fix** — touched: show.blade.php
- `fe1d6914` 2026-08-19 — **fix** — touched: MobileProfileController.php, layout.blade.php, form-submission.blade.php
- `61802d0d` 2026-08-24 — cron fix
- `696d0a5b` 2026-08-24 — Fix SWMS multi-row division, code QR redirect loop, and print report logo/number
- `704498b0` 2026-08-24 — Fix dead participant-list buttons in embed modal (single Livewire root)
- `73451a54` 2026-08-24 — Fix participant Clear list / delete buttons in embed (slConfirm fallback)
- `99b72ba8` 2026-08-24 — Fix submission report/log, QR name, and signature ordering
- `cf95f83e` 2026-08-24 — Use a valid 1x1 PNG in the signature-image presenter test
- `6ed1709f` 2026-08-24 — Fix scan page 500: multiline Blade comment inside @case broke @switch
- `b32df7c9` 2026-08-24 — Embed report PDF logo as data URI so TCPDF renders it
- `42be61f0` 2026-08-24 — Add repeatable Form Signature ("Add another") — #6
- `b99138e4` 2026-08-24 — Use GD-built PNG in signature presenter tests
- `cd8cd90b` 2026-08-24 — Batch A: VOCC title-bar required, code balance no-wrap, master-list inactive flag
- `70748570` 2026-08-24 — Batch B: scan-page header number, video URL parsing, section labels
- `fa35cd8f` 2026-08-24 — #6 gallery lightbox + #11 video above Words on the scan page
- `1fa708ef` 2026-08-24 — #14 Fix form-builder image not showing in "Create your form here"
- `2d5e6625` 2026-08-24 — Extend #9 (no section labels) + #6 (gallery lightbox) to Exhibit/VOC tiles
- `e7ffa0b8` 2026-08-24 — #2 Fix VOC Title Bar text clearing on save
- `04ce5b98` 2026-08-24 — #5/#2 Fix CKEditor clearing values after Livewire morphs; restore rich Title Bar
- `e463e142` 2026-08-24 — Fix locate-me feedback, red mandatory stars, library listing/preview, profile access, client email check
- `a8c9bfb6` 2026-08-24 — Fix portal-login expiry error (T39) and render profile Contacts on scan page (T26)
- `a075b4f8` 2026-08-24 — Hide duplicate "Reseller Pricing" entry from the admin sidebar
- `cfd86707` 2026-08-24 — T28: Fix Words heading checkboxes never re-enabling (MySQL enum index trap)
- `0bc95160` 2026-08-25 — T17: Redesign Download/Download All PDF as one flowing document
- `f821f820` 2026-08-25 — Staged batch: code balance scroll, header bar, Words gating, form-builder image+UI, profile access, VOC title bar
- `c3ed7402` 2026-08-25 — Replace CKEditor bridge with Filament's native RichEditor app-wide
- `b830a362` 2026-08-25 — Form-builder canvas: bullet-proof field geometry (no guessed selectors)
- `3e46b89d` 2026-08-25 — Remove per-video title labels above players on the scan page
- `417e9905` 2026-08-25 — Fix preview 500 (RichEditor draft array) + staged batch
- `f857522c` 2026-08-25 — Match legacy video modal per profile type (verified, not guessed)
- `4c18b6ea` 2026-08-25 — Video modal: validate YouTube input + fix local-video title link
- `a73b0a32` 2026-08-25 — Another account: per-user scope (all that user's forms) + Preview button
- `727a339d` 2026-08-25 — My Library: show the source code profile in rows and preview heading
- `c504622a` 2026-08-25 — Form preview renders the actual form (legacy get_data_for_form_preview parity)
- `660707eb` 2026-08-25 — Edit User: show all 8 permission checkboxes (Delete Profile was hidden)
- `8d9f2725` 2026-08-25 — Form builder: make every canvas control reliably clickable
- `18ab36db` 2026-08-25 — Form builder: remove uniform.js from the canvas entirely (root fix)
- `9264e2e5` 2026-08-25 — Empty-form selection shows a front-screen alert, not a corner toast
- `62010ac4` 2026-08-25 — Builder: unique element ids + drop-at-pointer; scan: Remove on added sections
- `a762c71c` 2026-08-25 — Submission media everywhere + form-order rendering + file previews
- `c008f3ed` 2026-08-25 — PDF shows every filled answer + legacy multi-image dropzones
- `99c1b4e5` 2026-08-25 — Document Button links everywhere + clean single-submission PDF header
- `544b52b8` 2026-08-25 — Redesign form submission PDF, view page, and print page with a clean production layout
- `df282bac` 2026-08-25 — Fix submission PDF/view/print layout: complex answers render as full-width sections (TCPDF cannot nest tables)
- `d7f69222` 2026-08-25 — Use full page width in submission PDF/view/print: narrower label column, larger photos and signatures
- `93eaad60` 2026-08-25 — Two-column submission layout: tighter card, images fill the value column edge-to-edge
- `9b4ce77e` 2026-08-26 — Render submission images at natural size capped as thumbnails (no upscaling) in view/print/PDF
- `22d83e4d` 2026-08-26 — Redesign form-builder drop elements: clean edit boxes, fixed color inputs, visible selects, aligned fields
- `573160de` 2026-08-26 — Fix lifecycle-test findings: human labels everywhere, display text on print/PDF, no chrome rows, doc-count validation, styled submit, nobr pagination
- `77194ec8` 2026-08-26 — Tighten Form Builder panel spacing: compact inputs, one-line Remove/Add Another, smaller format box
- `379b903f` 2026-08-26 — Compact form-builder canvas spacing; show current image/document when re-editing media elements
- `6d489ef0` 2026-08-27 — Visible validation errors on visitor form (banner, per-field highlight, scroll, kept input); scrollable submission-log table
- `1cce41dd` 2026-08-27 — Fix PDF h-tag blowups, log-table column widths, CID-embedded email images, picture tile previews, local video playback, existing-videos table with remove and pagination
- `a99dd12e` 2026-08-27 — Multi-submission PDF: unmissable solid green banner separating each submission
- `b5df4d58` 2026-08-27 — Legacy parity: visitor form shows only the labels the old app showed (mandatory star instead of invented type names)
- `ea212b9a` 2026-08-27 — Redesign form-submission email: clean single-card layout matching print/PDF, tamed rich-text headings, display context included
- `1039f04a` 2026-08-27 — Use the themed slAlert/slConfirm dialog everywhere: video remove, all form-builder alerts, participant iframe, visitor page
- `aae1046a` 2026-08-27 — Participant-list guard: count only non-empty recipient emails (exact legacy semantics)
- `ebba10b8` 2026-08-27 — Dark-mode submission view: readable values (inline-color overrides); live colour swatch beside builder colour-code inputs
- `a5883ead` 2026-08-27 — Unify submission typography: email-attached PDF now uses the shared Download PDF template; email/view/print font sizes matched to it
- `0186b5ee` 2026-08-31 — **fix** — touched: show.blade.php
- `0ddb8eab` 2026-08-31 — **fix** — touched: ProfileResource.php, show.blade.php, SubUserCreateProfileTest.php
- `e2705453` 2026-08-31 — **fix** — touched: legacy-preview-sidebar.blade.php
- `c54313a1` 2026-08-31 — **fix** — touched: MobileProfileController.php, show.blade.php, SwmsFormSubmissionTest.php
- `a6a5ac96` 2026-08-31 — **fix** — touched: ManageParticipants.php, scanlink-theme.css, participant_list_sample.xlsx, _participant-list-body.blade.php +1 more
- `c8ca0d49` 2026-08-31 — **fix** — touched: ManageParticipants.php, MobileProfileController.php, participant_list_sample.xlsx
- `34d9bf1e` 2026-08-31 — **fix** — touched: MobileProfileController.php, show.blade.php, SwmsFormSubmissionTest.php
- `c6b6ce82` 2026-08-31 — **fix** — touched: show.blade.php
- `26440044` 2026-08-31 — Fix mobile photo uploads: JS file store is authoritative, never input.files
- `e6027262` 2026-08-31 — Fix vanishing form photos: raise PHP upload limits, report failed uploads, stop fetch consuming the flash
- `a0950830` 2026-08-31 — **fix** — touched: CLAUDE.md, PROJECT_HISTORY.md, show.blade.php, SwmsFormSubmissionTest.php
- `da6b3fbe` 2026-08-31 — **fix** — touched: MobileProfileController.php
- `58e929bc` 2026-08-31 — Stop tracking local AI working docs
- `bd99c1d6` 2026-08-31 — Fix participant list upload doing nothing, and prove SWMS photo row targeting
- `11d1f55b` 2026-08-31 — Move upload preview remove button to the right; drop deleted videos from the form
- `33739c0f` 2026-08-31 — Show the selected date in the picker; fix sub-user "Add a New Code"
- `6dd7d123` 2026-08-31 — Downscale photos in the browser; fix dark-mode text on submission view and editor
- `5ecb8a93` 2026-08-31 — Drop a deleted video from the form through the Repeater, not the data array
- `cbd2c076` 2026-08-31 — Upload the participant list on button click, and stop the broken notification
- `efe8b25d` 2026-08-31 — Close the four VOCC parity gaps

## 2026-09 — 2 commits

- `a7eb72f0` 2026-09-01 — Track project knowledge and the 2026-08-31 work log under .claude/
- `f18c2e31` 2026-09-01 — Make the repo self-sufficient for handover: track CLAUDE.md and PROJECT_HISTORY.md
