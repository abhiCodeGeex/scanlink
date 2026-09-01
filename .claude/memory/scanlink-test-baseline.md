---
name: scanlink-test-baseline
description: Eight tests fail on develop for pre-existing reasons — attribute failures against this baseline before blaming a change
metadata: 
  node_type: memory
  type: project
  originSessionId: 6d31b29a-a11d-4965-817d-304291c7082e
  modified: 2026-09-01T05:06:07.769Z
---

As of 2026-09-01 on `develop`, the full suite is **93 tests with 8 pre-existing failures**. They are not regressions:

- `FormSubmissionPresenterTest::it_divides_multiple_swms_hazard_rows_into_instances` — expects `<hr>` in PDF markup that `swmsPdfHtml()` does not emit
- `FullAdminRegressionTest` ×2, `ProfileResourceTest::test_profile_create_validates_required_fields_for_selected_type`
- `ContactFormTest` ×2 — no `settings` table in the test schema
- `MinimalPortalAuthTest::test_portal_user_can_login_and_reach_home` — redirect target changed to `/portal/profiles`
- `PortalRegistrationTest::test_second_next_creates_account_sends_mail_shows_popup_and_logs_in` — mailable not sent

Also note: `/portal/profiles` **500s under sqlite** because it runs raw SQL using `NOW()`, which sqlite has no function for. Do not assert a 200 on that page in tests.

**Why:** a red suite is easy to misread as "my change broke it". Twice this baseline was the difference between a real finding and a wild goose chase.

**How to apply:** before concluding a change caused a failure, run the same filter against the pre-change commit and compare counts. See [[scanlink-running-tests]] for how to run it.
