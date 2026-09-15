# AI Agent Notepad

Corrections and learnings logged here during sessions.
Review monthly — promote recurring patterns (3+ occurrences) to `.ai-tools/rules/`.

## Corrections
<!-- Entries added during sessions. Format: [Count: N] YYYY-MM-DD - Description -->

[Count: 1] 2026-09-11 - Comments must not narrate the bug that was fixed. A comment explaining
why the OLD code was wrong (e.g. "must not silently disappear via list-assignment") describes
history, not the current code's WHY - that rationale belongs in the commit message. Same defect:
naming a function the code no longer calls ("all of which \is_numeric() would accept"). Sharpens
code-quality.md "Comments: WHY Only, Never WHAT". Seen in NetworkAddressValidationTrait.
