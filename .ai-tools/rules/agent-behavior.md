---
description: "Agent behavior: scope discipline, dropping fragile features, proposing rule updates after repeated corrections"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Agent Behavior

## Drop Problematic Features Early

Drop a feature when: testing is impractical, version compatibility requires excessive workarounds, or the implementation would make the codebase fragile.

## Ask Before Expanding Scope

**Required**: When a fix reveals adjacent problems, describe them and ask whether they are in scope before touching them.

```
// ❌ Wrong — silently rename methods or fix unrelated tests while fixing a bug
// ✓ Correct — "I also noticed X, want me to fix it?"
```

## Propose Rule Updates After Repeated Corrections

After 2+ corrections on the same topic in a session, propose a `.ai-tools/rules/` update before the session ends.
