# Generate session handover

Summarize the current session for continuity. Save to `.claude/handovers/HANDOVER-<timestamp>.md`.

## Steps

1. Gather context via `git branch --show-current`, `git log --oneline main..HEAD`, `git status --short`, `git diff --name-only main`

2. Write the handover file covering: branch, objective, work completed, key decisions, current state (committed/uncommitted/passing/failing), open items and next steps.

3. End the file with a **Resume Prompt** — a single quoted sentence the user can paste to start the next session, e.g.: `Read .claude/handovers/HANDOVER-<timestamp>.md and continue work on <branch>. <next action>.`

4. Print the resume prompt so the user can copy it.
