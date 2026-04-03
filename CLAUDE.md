# PostgreSQL for Doctrine — AI Instructions

@AGENTS.md

## Quick Reference

| Task | How | Key rules |
|------|-----|-----------|
| New DBAL type | `/new-dbal-type` skill | exceptions, since-annotations |
| New DQL function | `/new-dql-function` skill | variadic-functions, since-annotations |
| Fix failing tests | `composer run-unit-tests`, filter with `--filter` | testing-and-iteration, test-data-management |
| Pre-PR quality check | `/run-quality-gate` skill | phpstan-compliance, code-quality |
| Session handover | `/handover` skill | — |

## Hooks

- **PostCompact**: Re-injects critical rules after context compaction
- **PostToolUse**: Auto-formats PHP files after Edit/Write

## Persistent Learning

Session corrections are logged in `.claude/notepad.md`. Read it at the start of each session. When a correction appears 3+ times, promote it to `.ai-tools/rules/`.
