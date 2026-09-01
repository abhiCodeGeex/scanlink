---
name: scanlink-two-checkouts
description: Two diverged ScanLink checkouts exist; git/scanlink is the live one and CLAUDE.md points at the wrong one
metadata: 
  node_type: memory
  type: project
  originSessionId: 6d31b29a-a11d-4965-817d-304291c7082e
  modified: 2026-09-01T05:05:33.506Z
---

There are two ScanLink Laravel checkouts on this machine and they are **different repos**, not copies:

- `E:\abhishek-project\git\scanlink` — remote `nwitadmin/scanlink`, branch `develop`. **This is where work happens.**
- `E:\abhishek-project\scanlink-laravel` — remote `abhiCodeGeex/scanlink`, branch `full-code`, last commit 2026-08-31 morning. Older, with uncommitted WIP.

`CLAUDE.md` says "New app (this repo) | E:\abhishek-project\scanlink-laravel" — that line is **stale and misleading**; it was copied in from the older checkout.

The divergence is large, not cosmetic: as of 2026-09-01 `resources/views/scan/show.blade.php` is 2117 lines in git/scanlink vs 1555 in scanlink-laravel, and scanlink-laravel has none of the mobile-upload rework (`slBuildFormDataWithFiles` absent).

**Why:** confusing the two wastes a lot of time — the local Docker stack bind-mounts `scanlink-laravel`, so `localhost:8000` serves the OLD checkout while edits land in `git/scanlink`.

**How to apply:** edit in `git/scanlink`. Never assume `localhost:8000` reflects your changes — see [[scanlink-staging-server]]. Porting between the two is not a clean patch apply; the prerequisite commits are missing.
