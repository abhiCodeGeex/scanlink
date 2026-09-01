---
name: abhishek-verify-before-committing
description: "The user wants fixes proven in a real browser before they are committed — \"I don't want rework\""
metadata: 
  node_type: memory
  type: feedback
  originSessionId: 6d31b29a-a11d-4965-817d-304291c7082e
  modified: 2026-09-01T05:06:17.965Z
---

Stated directly: *"fix this i dont want rework, test it from your side then update in branch"*, and earlier *"i dont want guessings please make it solid fix"*.

They test on staging themselves and come back when something is still broken, so a plausible-looking fix that was never exercised costs them a round trip.

**Why:** twice in one session a fix that passed PHPUnit was still broken in the browser — Livewire's test harness rebuilds Filament repeater child schemas from state on each access, so it cannot reproduce a stale render at all. Tests alone are not evidence for anything UI-shaped.

**How to apply:**
- Reproduce the bug first and show the measurement, not a theory.
- For UI/JS fixes, drive the real page (Playwright) and verify the actual behaviour before committing; injecting the exact shipped code into the live page works when staging still runs the old build.
- Where a test genuinely cannot cover something, say so in the test file rather than leaving assertions that imply coverage they do not provide.
- State plainly what was verified and what was not. See [[scanlink-staging-server]] for the deploy caveat.
