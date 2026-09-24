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

## Integration Tests Cover Our Call, Not PostgreSQL's Behaviour

An integration test proves the DQL function reaches PostgreSQL and the result hydrates. What PostgreSQL then does with the value is PostgreSQL's contract, already tested upstream.

**Every function owes two tests, and they are the minimum:**

1. a **literal** — the function applied to a string the DQL itself spells, proving the argument parses
2. an **entity field** — the function applied to a recorded column, proving a stored value binds and hydrates

Beyond those two, add a test only for another **arity**. A test that just changes the input geometry, the index or the row repeats a call already covered and asserts a PostgreSQL rule instead of ours.

Both halves take the **same verb from the table in `test-naming-patterns.md`** and name the same subject — only the source differs. Two verbs for one assertion is a naming difference dressed as a behavioural one.

```php
// ✓ The two halves every function owes — one verb, one subject, two sources
returns_the_first_vertex_from_a_wkt_literal()    // ST_ASTEXT(ST_STARTPOINT('LINESTRING(0 0,1 1,2 2)'))
returns_the_first_vertex_from_an_entity_field()  // ST_ASTEXT(ST_STARTPOINT(g.geometry1))

// ❌ Same assertion, two verbs
parses_the_first_vertex_from_a_wkt_literal()
returns_the_first_vertex_of_an_entity_field()

// ❌ One arity, and the second asserts a PostGIS rule
returns_first_vertex_of_linestring()
returns_first_vertex_of_polygon_ring()

// ❌ Same — null for an out-of-range index is PostGIS's rule
returns_null_for_out_of_range_index()
```

**The literal half keeps an entity field when the function takes more than one argument.** With every operand a literal, `FROM … WHERE g.id = N` contributes nothing — the query answers the same for any row, and the filter only looks like it matters.

```php
// ❌ both operands literal — the fixture row is decorative
BOUNDING_BOX_DISTANCE('POLYGON((0 0, 2 0, 2 2, 0 2, 0 0))', 'POLYGON((1 1, 3 1, 3 3, 1 3, 1 1))')

// ✓ the row is load-bearing and the literal still proves literal parsing
GEOMETRY_DISTANCE(g.geometry1, 'SRID=4326;POINT(1 1)')
GEOMETRY_DISTANCE(g.geometry1, g.geometry2)
```

A single-argument function has no mixed form, so an all-literal call is its only literal test; there the `WHERE` just limits the result to one row.

**Both halves read the same fixture row and assert the same value** — that is what proves the two sources agree. On different rows they are two unrelated tests.

```php
// ❌ each half on its own row, so the answers have nothing to do with each other
BOUNDING_BOX_DISTANCE(g.geometry1, 'POLYGON((1 1, 3 1, 3 3, 1 3, 1 1))')  // g.id = 1 → 1.4142135623730951
BOUNDING_BOX_DISTANCE(g.geometry1, g.geometry2)                           // g.id = 2 → 0

// ✓ one row, one answer
BOUNDING_BOX_DISTANCE(g.geometry1, 'POLYGON((1 1, 3 1, 3 3, 1 3, 1 1))')  // g.id = 1 → 1.4142135623730951
BOUNDING_BOX_DISTANCE(g.geometry1, g.geometry2)                           // g.id = 1 → 1.4142135623730951
```

A third test earns its place only on another arity, and takes `<verb>_the_<subject>_with_<argument>` — the pair's verb and subject, then the extra argument itself, never its value or the outcome.

```php
// ❌ names the input's shape, or the result
returns_buffered_point_with_quad_segs_parameter()
returns_inner_hull_contained_by_original()

// ✓ same family as the pair, distinguished by the argument
returns_the_buffered_geometry_with_quad_segs()
returns_the_simplified_hull_with_is_outer()
```

Reach the function directly. Nest a helper only to build an input the fixtures do not hold:

```php
// ❌ Four deep to reach one function
ST_X(ST_STARTPOINT(ST_GEOMETRYN(ST_COLLECT(g.geometry1, g.geometry2), 1)))

// ✓ A literal says what the input is. A bare WKT string needs no ST_GEOMFROMTEXT -
//   PostgreSQL casts unknown to geometry, and PostGIS ships text overloads
ST_ASTEXT(ST_GEOMETRYN('MULTIPOINT((1 2),(3 4))', 1))

// ✓ ST_COLLECT earns its place — no fixture column holds a collection
ST_ASTEXT(ST_GEOMETRYN(ST_COLLECT(g.geometry1, g.geometry2), 1))
```

A bare literal arrives as `unknown`, so an **overloaded** operator can bind the wrong one and still pass. `'POINT(0 0)' ~ 'POINT(1 1)'` binds `textregexeq`, not `geometry_contains`. Type one operand, or make the class a documented exemption.

Assert a computed geometry only after checking the value is identical on the oldest and newest PostGIS in `.github/workflows/integration-tests.yml`.

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

