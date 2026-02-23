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

**Do**: `'StringPrimary,ArithmeticPrimary,StringPrimary'` for `(geometry, float, boolean)`
**Don't**: `'StringPrimary,ArithmeticPrimary,ArithmeticPrimary'`

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

**Do**: `['StringPrimary,ArithmeticPrimary,StringPrimary', 'StringPrimary,ArithmeticPrimary']`
**Don't**: `['StringPrimary,ArithmeticPrimary', 'StringPrimary,ArithmeticPrimary,StringPrimary']`

