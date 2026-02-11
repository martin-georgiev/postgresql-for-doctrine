# Code Quality

## Remove Obvious Comments
Comments that restate what the code does add noise without value. Keep comments that explain *why* (design decisions, non-obvious behavior) but remove comments that explain *what* when the code is self-explanatory.

**Remove**: `// Original LINESTRING has length 2*sqrt(2) ≈ 2.828`
**Keep**: Comments explaining PostgreSQL-specific behavior or architectural decisions

## Prefer Strong Assertions
Avoid weak or brittle assertions that could pass for wrong reasons. Use precise assertions that verify the actual expected behavior.

**Weak**: `assertStringContainsString(',2)', $result)` - could match unintended patterns
**Strong**: `assertEqualsWithDelta(2.0, $result, 0.001)` - verifies exact expected value

