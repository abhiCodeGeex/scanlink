---
name: scanlink-staging-server
description: Staging runs at 13.237.243.130:8000 and is deployed manually — local Docker serves a different checkout
metadata: 
  node_type: memory
  type: reference
  originSessionId: 6d31b29a-a11d-4965-817d-304291c7082e
  modified: 2026-09-01T05:05:44.873Z
---

Staging: **http://13.237.243.130:8000** — this is where the client tests, and where real profiles live (e.g. profile 6771 at `/northwestitservices/6771`, profile 6770 is an Exhibit).

It is **not** fed by the local Docker stack. `localhost:8000` bind-mounts `E:\abhishek-project\scanlink-laravel`, the older checkout — see [[scanlink-two-checkouts]]. Staging is deployed separately and by hand, so committed work is not live until someone deploys it.

Local Docker MySQL only goes up to profile id 6661, so any profile the client mentions above that exists only on staging.

Portal login is a modal on the marketing home page (`POST /portal-login`); there is no `/portal/login` route.

**Why:** several hours were lost diagnosing a bug against the wrong build. Always confirm which build is serving before drawing conclusions from the browser.

**How to apply:** check a deployed marker before testing — e.g. grep the served page for a string only the new code has. Tell the user explicitly when a fix is committed but not deployed.
