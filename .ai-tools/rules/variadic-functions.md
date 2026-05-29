---
description: "Variadic DQL functions: boolean StringPrimary, BooleanValidationTrait, node pattern ordering"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Variadic Function Development

## Boolean Parameters in DQL
DQL does not support native boolean literals (`true`/`false`). For variadic functions with boolean optional parameters, use `StringPrimary` in the node mapping pattern, not `ArithmeticPrimary`. Users must pass booleans as string literals in DQL queries.

```
// ✓ Correct — booleans pass through StringPrimary
'StringPrimary,ArithmeticPrimary,StringPrimary'   // (geometry, float, boolean)

// ❌ Wrong — ArithmeticPrimary cannot carry a string boolean literal
'StringPrimary,ArithmeticPrimary,ArithmeticPrimary'
```

**DQL usage**: `ST_CONCAVEHULL(g.geometry, 0.99, 'true')` not `ST_CONCAVEHULL(g.geometry, 0.99, true)`

## Boolean Parameter Validation
Functions with boolean parameters must validate them using `BooleanValidationTrait`. This ensures users pass valid `'true'` or `'false'` string literals.

**Required implementation**:
1. Add `use BooleanValidationTrait;` to the class
2. Override `validateArguments()` to call `$this->validateBoolean()` on the boolean argument
3. Add a unit test that verifies `InvalidBooleanException` is thrown for invalid values

**Example**:
```php
use BooleanValidationTrait;

protected function validateArguments(Node ...$arguments): void
{
    parent::validateArguments(...$arguments);

    if (\count($arguments) === 3) {
        $this->validateBoolean($arguments[2], $this->getFunctionName());
    }
}
```

## Node Mapping Pattern Ordering
When defining multiple node mapping patterns for variadic functions, order patterns from most arguments to fewest. The parser tries patterns in sequence and stops at the first successful match.

```
// ✓ Correct — longest pattern first
['StringPrimary,ArithmeticPrimary,StringPrimary', 'StringPrimary,ArithmeticPrimary']

// ❌ Wrong — shorter pattern first, three-arg form never matched
['StringPrimary,ArithmeticPrimary', 'StringPrimary,ArithmeticPrimary,StringPrimary']
```

## Homogeneous Arguments: Use Single-Element Pattern

When all arguments share the same node type, use a **single-element array**. `BaseVariadicFunction` detects `count($nodeMapping) === 1` (`$isNodeMappingASimplePattern = true`) and reuses index 0 for every argument. Arity is enforced separately by `getMinArgumentCount()`/`getMaxArgumentCount()`.

```php
// ✓ Idiomatic — all args are StringPrimary, arity enforced by min/max
protected function getNodeMappingPattern(): array { return ['StringPrimary']; }
protected function getMinArgumentCount(): int { return 2; }
protected function getMaxArgumentCount(): int { return 3; }

// ❌ Redundant — explicit multi-pattern is only needed when arg positions differ in type
protected function getNodeMappingPattern(): array {
    return ['StringPrimary,StringPrimary,StringPrimary', 'StringPrimary,StringPrimary'];
}
```

Use multi-pattern only when argument positions require **different** node types (e.g. `StringPrimary,ArithmeticPrimary`).

