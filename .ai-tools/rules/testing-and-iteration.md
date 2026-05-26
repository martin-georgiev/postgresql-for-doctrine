---
description: "Testing and iteration: version checks, targeted runs, 3-attempt limit, WKB geometry assertions, getSQLDeclaration blind spot, fixture data management"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Testing and Iteration

## Handle Version-Specific Features

**Rule**: Add a version check only when the CI matrix tests PostgreSQL/PostGIS versions older than the feature's minimum requirement. Otherwise skip the check.

Source of truth for tested versions: `.github/workflows/integration-tests.yml`.

Version-number format: `XXYYZZ` (e.g. `180000` = PostgreSQL 18.0.0; `30500` = PostGIS 3.5.0).

```php
// ✓ Add check — CI tests PostGIS 3.4, 3.5, 3.6 but ST_HasM requires 3.5+
protected function setUp(): void
{
    parent::setUp();
    $this->requirePostgisVersion(30500, 'ST_HasM');
}

// ❌ Don't add check — all tested versions (3.4+) support ST_LineExtend
```

## Run Targeted Tests After Code Changes

**Required**: Filter to the affected test class/method instead of running the whole suite.

```bash
bin/phpunit --filter "ST_HasZ" --configuration ci/phpunit/config-integration.xml
```

Use `composer test:unit` / `composer test:integration` only when full-suite coverage is genuinely needed.

## Iteration Limit on Test Failures

**Rule**: Maximum **3 attempts** to fix a failing test by modifying implementation code. After 3 failures, stop and ask the user. Prevents trial-and-error debt.

## DBAL Types: `getSQLDeclaration()` Is Not Tested by Integration Tests

Integration tests create tables with raw SQL and **never call `getSQLDeclaration()`**. A type that emits `VECTOR` instead of `VECTOR(1024)` is invisible to them.

**Required**: Every DBAL type that maps to a parameterized PostgreSQL type provides a proper SQL declaration AND unit tests for it.

Common PostgreSQL parameterization patterns:

| Pattern | Examples |
|---------|---------|
| `TYPE(n)` — length / dimensions | `BIT(n)`, `VARCHAR(n)`, `VECTOR(n)`, `HALFVEC(n)`, `SPARSEVEC(n)` |
| `TYPE(p, s)` — precision + scale | `NUMERIC(p, s)`, `DECIMAL(p, s)` |
| `TYPE(p)` — fractional-second precision | `TIMESTAMP(p)`, `TIMESTAMPTZ(p)`, `TIME(p)` |
| `TYPE(subtype, srid)` — PostGIS | `GEOMETRY(type, srid)`, `GEOGRAPHY(type, srid)` |

- **`TYPE(n)` cases**: use `LengthAwareSQLDeclarationTrait` (`src/.../Types/Traits/`) — it reads `fieldDeclaration['length']` and fulfills the override requirement without writing the method manually.
- **Other parameterizations** (precision/scale, SRID, etc.): override `getSQLDeclaration()` directly and read the appropriate `$fieldDeclaration` keys (`'precision'`, `'scale'`, `'srid'`, etc.).

Required unit tests for any override:
1. No parameters → bare type name
2. Parameters provided → correct parameterized form (e.g. `VECTOR(1024)`)

### Integration regression guard for parameterized declarations

Unit tests verify the string `getSQLDeclaration()` returns, but won't catch a regression that drops the parameters at runtime. Required guard:

1. Route the test column type through `getSQLDeclaration()` (override `getFieldDeclaration()` on the integration test) so the production declaration is exercised end-to-end.
2. Add at least one integration test that inserts a value rejected only when the constraint is present (e.g. a vector with mismatched dimension, a bit string longer than declared width). Expect `Doctrine\DBAL\Exception\DriverException`.

If the regression returns, the bare `TYPE` declaration would silently accept the bad value and the `expectException` test fails — making the regression visible.

## PostGIS Geometry Result Assertions

PostGIS geometry results are WKB hex, not WKT. **Never** assert on the raw string. Use a wrapping function whose result has a stable type:

| Need | Function family | Result type |
|------|-----------------|-------------|
| Equality | `ST_EQUALS(geom1, geom2)` | boolean |
| Measurement | `ST_LENGTH`, `ST_AREA`, `ST_DISTANCE` | numeric |
| Relationship | `ST_CONTAINS`, `ST_INTERSECTS`, `ST_RELATE` | boolean / DE-9IM string |
| Null-or-not | direct function | use `assertNull()` / `assertNotNull()` |

```php
// ✓ Correct — assert on the numeric result of a measurement function
$dql = 'SELECT ST_LENGTH(ST_CURVEN(g.geometry1, 1)) as result FROM ...';
$this->assertEqualsWithDelta(1.4142, $result[0]['result'], 0.001);

// ❌ Wrong — geometry result is WKB hex, not WKT, so the substring never matches
$dql = 'SELECT ST_CURVEN(g.geometry1, 1) as result FROM ...';
$this->assertStringContainsString('LINESTRING', $result[0]['result']);
```

## Fixture Data: Use Central Fixtures

**Required**: Reference existing central fixture IDs — do not create test-specific `INSERT` statements in test methods.

```php
// ❌ Wrong — test-local data insertion
private function insertSpecialData(): void { $this->connection->executeStatement('INSERT ...'); }

// ✓ Correct — references central fixture by ID
$this->runDqlQuery('... WHERE e.id = 4');
```

## Fixture Data: Resilient to Growth

**Forbidden**: Assert exact row counts derived from total fixture size — new fixtures will break them.

```php
// ❌ Wrong — count depends on total fixture size
$this->assertCount(10, $result);

// ✓ Correct — filter to specific IDs, or accept that counts may grow
$dql = 'SELECT ... WHERE e.id IN (1, 2, 3)';
```

**Before adding new fixtures**:
1. Search for tests that don't filter by specific IDs
2. Check if any spatial functions have limitations with the new geometry type
3. Add explicit ID filters to known compatible fixtures (e.g., `WHERE id = 4` or `WHERE id IN (1, 2, 3)`)

**Example**: PostGIS 3.4 doesn't support `ST_DFullyWithin` with arc geometries (CircularString/CompoundCurve). Tests scanning all rows will fail when CompoundCurve fixtures are added.

