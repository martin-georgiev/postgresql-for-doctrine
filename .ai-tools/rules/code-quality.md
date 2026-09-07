---
description: "Code quality standards: avoid obvious comments, anchor validation regexes with \\z, use strong assertions, never cast actual values in assertions"
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

## Validation Regexes: Anchor With `\z`, Not `$`
**Required**: End validation patterns with `\z`. In PCRE, `$` also matches immediately before a trailing newline, so a `$`-anchored pattern accepts values PostgreSQL rejects.

```php
// ❌ Wrong — also matches "101\n", which PostgreSQL rejects for bit
\preg_match('/^[01]+$/', $value)

// ✓ Correct — matches only "101"
\preg_match('/^[01]+\z/', $value)
```

**Exception**: `$` is harmless only when the value is parsed into a typed or canonical form before it reaches the database, so the trailing newline is discarded on the way (the geometric value objects re-emit `(1,2)`; the integer and float array types cast). Anchor with `\z` everywhere else — including when PostgreSQL *accepts* the trailing whitespace, because a type that writes the item verbatim (`numeric[]`) then reads back a trimmed value, and a type that reformats it (`macaddr`) can build a malformed one.

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

