---
name: scanlink-credential-in-pushed-history
description: A test portal password sits in PROJECT_HISTORY.md inside pushed git history and is still unrotated
metadata: 
  node_type: memory
  type: project
  originSessionId: 6d31b29a-a11d-4965-817d-304291c7082e
  modified: 2026-09-01T05:06:27.760Z
---

`PROJECT_HISTORY.md` contained a live portal test credential (an email/password pair) at lines 40 and 256. The file was committed in `a0950830` and **pushed** to `github.com/nwitadmin/scanlink`. The value is deliberately not repeated here — read it from that commit if you need to identify the account.

On 2026-08-31 (`58e929bc`) the file was added to `.gitignore` and removed from the index, so it no longer sits at the branch tip. That does **not** remove it from history: `git show a0950830:PROJECT_HISTORY.md` still returns it.

Outstanding as of 2026-09-01: the password has not been reported as rotated, and history has not been rewritten.

**Why:** untracking a file reads like "handled" and isn't. Anyone with repo access can still read the password.

**How to apply:** if the account is still in use, recommend rotating it — far more reliable than scrubbing history. Purging it properly needs `git filter-repo` plus a force-push to a shared branch, which rewrites commits others have pulled; get explicit approval before suggesting that route.
