---
description: "Code quality standards: avoid obvious comments, use strong assertions, never cast actual values in assertions"
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

## Assertions: No Type Casting on the Actual Value
**Required**: Pass the raw actual value to every assertion — never cast it before asserting.
**Forbidden**: Casting the actual result to make it fit the expected type. Casting hides type mismatches that are themselves bugs.

```php
// ❌ Wrong — casting disguises a type mismatch as a passing test
$this->assertTrue((bool) $result[0]['result']);
$this->assertSame('1', (string) $result[0]['result']);
$this->assertSame(1, (int) $result[0]['result']);

// ✓ Correct — assert the raw value; let the assertion fail if the type is wrong
$this->assertTrue($result[0]['result']);
$this->assertSame('1', $result[0]['result']);
$this->assertSame(1, $result[0]['result']);
```

**Casting the expected literal is fine** — the expected value is under your control and you choose its type deliberately:

```php
// ✓ Fine — you are declaring the expected type explicitly
$this->assertSame(1, $result[0]['result']);       // expect integer
$this->assertSame('1', $result[0]['result']);     // expect string

// ❌ Still wrong — even with a cast on the actual side
$this->assertSame(1, (int) $result[0]['result']); // masks a string result
```

**Exception — justified casts**: A cast on the actual side is only acceptable when the value's declared return type is `mixed` or `string` and the intent is to assert on a narrowed sub-value (e.g. decoding JSON before asserting its fields). Always add a comment explaining why the cast is safe and what bug it could hide if removed.

