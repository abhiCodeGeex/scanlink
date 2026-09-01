# `.claude/` — project knowledge

Durable notes about *working on* this repo: things that are not derivable from the code,
the git history, or `CLAUDE.md`. Committed deliberately so they survive between sessions
and are available to everyone, not just one machine.

| Path | What it is |
|---|---|
| `memory/MEMORY.md` | Index of the facts below — read this first |
| `memory/*.md` | One fact per file: environment gotchas, working preferences, open risks |
| `WORK-LOG-2026-08-31.md` | Full account of the 2026-08-31 session: every bug, root cause, fix and verification |

## Handing this over to another developer

Everything Claude needs is in the repo — clone it and Claude Code picks up `CLAUDE.md`
automatically. Nothing else needs installing.

What you still have to obtain out-of-band, because it is deliberately not in the repo:

| Needed | Why |
|---|---|
| The legacy Kohana app | Behavioural source of truth. Paths in `CLAUDE.md` are from the original dev machine — put it anywhere and update that table. |
| The MySQL import | Docker MySQL holds a full live import. Without it, most portal pages have no data. |
| Portal test credentials | Never recorded here. Ask the project owner. |
| Staging deploy access | Staging is deployed by hand; committing does not ship anything. |

First session checklist:

1. Read `CLAUDE.md`, then `memory/MEMORY.md` (six short files).
2. Read `WORK-LOG-2026-08-31.md` if you are touching form submissions, the participant list,
   videos, dark mode or VOCC — it records why several fixes look the way they do.
3. Run the suite using `memory/scanlink-running-tests.md` and confirm you reproduce the
   baseline in `memory/scanlink-test-baseline.md` before changing anything.

## Read these before starting work

- **`memory/scanlink-two-checkouts.md`** — there are two diverged checkouts and `CLAUDE.md`
  names the wrong one as "this repo". The local Docker stack serves the *other* one.
- **`memory/scanlink-running-tests.md`** — there is no `vendor/` here; tests need a specific
  container recipe. A Windows bind mount silently hangs.
- **`memory/scanlink-test-baseline.md`** — 8 tests are already red. Attribute failures against
  that list before assuming your change caused them.

## Rules for this directory

- **No credentials, tokens or passwords.** A test login was once committed in
  `PROJECT_HISTORY.md` and pushed; untracking it did not remove it from history. See
  `memory/scanlink-credential-in-pushed-history.md`.
- One fact per memory file, with the frontmatter block the existing files use.
- Update `memory/MEMORY.md` when adding a file.
- Correct a memory rather than adding a second one that contradicts it.

`CLAUDE.md` and `PROJECT_HISTORY.md` at the repo root **are tracked**, so a fresh clone is
enough for another developer to start. The credential that was once in `PROJECT_HISTORY.md`
has been removed from the working copy — but it is still readable in pushed history, and that
is a separate problem which untracking never solved. See
`memory/scanlink-credential-in-pushed-history.md`.
