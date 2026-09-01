# `.claude/` — project knowledge

Durable notes about *working on* this repo: things that are not derivable from the code,
the git history, or `CLAUDE.md`. Committed deliberately so they survive between sessions
and are available to everyone, not just one machine.

| Path | What it is |
|---|---|
| `memory/MEMORY.md` | Index of the facts below — read this first |
| `memory/*.md` | One fact per file: environment gotchas, working preferences, open risks |
| `WORK-LOG-2026-08-31.md` | Full account of the 2026-08-31 session: every bug, root cause, fix and verification |

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

`CLAUDE.md` and `PROJECT_HISTORY.md` at the repo root remain **untracked and gitignored**
(they were committed unintentionally, and `PROJECT_HISTORY.md` still carries the credential
in pushed history). They stay on disk; this directory is the tracked replacement.
