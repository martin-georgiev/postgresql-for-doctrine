---
description: "Variadic DQL functions: numeric SimpleArithmeticExpression, boolean StringPrimary, BooleanValidationTrait"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Variadic Function Development

## Numeric Parameters in DQL
Read a numeric argument with `SimpleArithmeticExpression`, never `ArithmeticPrimary`. `ArithmeticPrimary` takes no sign, so DQL rejects `ST_MAKEPOINT(-71.1, 42.3)` before PostgreSQL sees it. `composer run-static-analysis` flags it in a node mapping pattern, in `addNodeMapping()` and in a direct parser call.

## Boolean Parameters in DQL
DQL has `TRUE` and `FALSE` literals, and `SimpleArithmeticExpression` accepts them as well as string literals; `StringPrimary` accepts only the string. For variadic functions with boolean optional parameters, use `StringPrimary` in the node mapping pattern, not `SimpleArithmeticExpression`, so every boolean argument has one spelling: the string literal `'true'` or `'false'`.

```
// ✓ Correct — booleans pass through StringPrimary
'StringPrimary,SimpleArithmeticExpression,StringPrimary'   // (geometry, float, boolean)

// ❌ Wrong — SimpleArithmeticExpression also accepts a bare TRUE, a second spelling
'StringPrimary,SimpleArithmeticExpression,SimpleArithmeticExpression'
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
