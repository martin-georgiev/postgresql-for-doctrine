---
description: "Code quality standards: avoid obvious comments, use strong assertions"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Code Quality

## Comments: WHY Only, Never WHAT
**Required**: Add a comment only when the WHY is non-obvious — a hidden constraint, a subtle invariant, or a specific workaround.
**Forbidden**: Comments that restate what the code already expresses through names and structure.

```php
// ❌ Remove — restates the obvious
// Original LINESTRING has length 2*sqrt(2) ≈ 2.828

// ✓ Keep — explains non-obvious PostgreSQL behavior or architectural decision
// PostgreSQL normalizes POINTZ → POINT Z on retrieval; normalize on write too
```

## Assertions: Exact Values, Not Substring Matches
**Required**: Assert the precise expected value.
**Forbidden**: Substring or partial matches that could pass for wrong reasons.

```php
// ❌ Weak — passes for any string containing ",2)"
$this->assertStringContainsString(',2)', $result);

// ✓ Strong — verifies the actual numeric result
$this->assertEqualsWithDelta(2.0, $result, 0.001);
```

