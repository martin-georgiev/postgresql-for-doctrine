# Create a new DBAL type: $ARGUMENTS

## 1. Identify the domain group

Each group has a distinct style. Pick the right group and use its reference — not a type from a different group.

| Group | Scalars | Arrays | What's distinct |
|-------|---------|--------|----------------|
| Network | `Inet`, `Cidr`, `Macaddr`, `Macaddr8` | via `BaseNetworkTypeArray` | Validation traits extending `NetworkAddressValidationTrait` |
| Geometric | `Point`, `Box`, `Circle`, `Line`, `Lseg`, `Path`, `Polygon` | via `BaseGeometricArray` | Per-type value objects in `ValueObject/`; `;`-separated arrays |
| Range | `Int4Range`, `Int8Range`, `NumRange`, `DateRange`, `TsRange`, `TstzRange` | — | `BaseRangeType` + value objects extending `Range` |
| Multirange | `Int4Multirange`, `Int8Multirange`, `NumMultirange`, etc. | — | `BaseMultirangeType` + value objects extending `Multirange` |
| PostGIS spatial | `Geometry`, `Geography` | via `SpatialDataArray` | `BaseSpatialType`, shared `WktSpatialData`, EWKB→EWKT SQL |
| Vector (pgvector) | `Vector`, `Halfvec`, `Sparsevec` | — | `BaseVector`, PHP float arrays, finiteness checks |
| Integer arrays | — | `IntegerArray`, `BigIntArray`, `SmallIntArray` | `BaseIntegerArray` with min/max bounds |
| Float arrays | — | `RealArray`, `DoublePrecisionArray` | `BaseFloatArray` with precision limits |
| DateTime arrays | — | `DateArray`, `TimestampArray`, `TimestampTzArray` | `BaseDateTimeArray`, format strings, returns `DateTimeImmutable[]` |
| JSON | `Jsonb` | `JsonbArray` | `JsonTransformer` trait, custom array transformers |

Types outside these groups have individual styles. Read the specific type you're closest to: `Tsvector`, `Tsquery`, `Money`, `Xml`, `Interval`, `Ltree`, `TextArray`, `UuidArray`, etc. Their arrays also vary — check what the existing array actually extends before assuming a base class.

Read your chosen reference file AND its base class AND its tests before writing code.

## 2. Update the type constant

Check `src/MartinGeorgiev/Doctrine/DBAL/Type`. Add the PostgreSQL type name if not listed.

## 3. Create source files

Follow the reference type's file structure. Every DBAL type needs domain-specific exceptions:
- Scalar types: `Invalid{TypeName}ForPHPException` + `Invalid{TypeName}ForDatabaseException`
- Array types: `Invalid{TypeName}ArrayItemForPHPException` + `Invalid{TypeName}ArrayItemForDatabaseException`

All exceptions in `src/.../Types/Exceptions/`, extend `ConversionException`, use static factory methods.

If the reference type has a value object, create one following the same pattern.

**Adding an array variant of an existing scalar type**: Extract shared validation logic from the scalar type into `src/.../Types/Traits/{TypeName}ValidationTrait` and refactor the scalar type to use it. The array type then also uses the same trait. See `MoneyValidationTrait`, `XmlValidationTrait` for examples. This modifies the existing scalar type and its tests.

## 4. Register the type for integration tests

Add the new type to `tests/Integration/MartinGeorgiev/TestCase`:
- Add a `use` import for the type class
- Add an entry to the `$typesMap` array (alphabetical order by key)

This is required for ALL new DBAL types — without it, integration tests cannot use the type.

## 5. Add @since tag

Check: `gh pr list --repo martin-georgiev/postgresql-for-doctrine --state open --search "release"`. NEVER guess.

## 6. Create tests

Both unit AND integration tests are **required** for every new type. Before writing, read the matching reference test from the same domain group — copy its structure exactly.

### Unit tests → `tests/Unit/.../Types/`

Cover: type name, null handling, valid round-trips (data provider), invalid types, invalid formats. Array types also: `isValidArrayItemForDatabase()`, `transformArrayItemForPHP()`.

Reference by group: `XmlTest`/`XmlArrayTest` (string-based), `MoneyTest`/`MoneyArrayTest`, `MacaddrTest`/`MacaddrArrayTest` (network), `PointTest`/`PointArrayTest` (geometric), `BaseRangeTestCase` (range), `BaseFloatArrayTestCase`/`BaseIntegerArrayTestCase` (numeric arrays).

### Integration tests → `tests/Integration/.../Types/`

Extend the base class matching your group. Each provides common tests automatically — you add `getTypeName()`, `getPostgresTypeName()`, a `provideValidTransformations()` data provider, and at least one rejection test.

| Group | Extend |
|-------|--------|
| Scalar | `ScalarTypeTestCase` |
| Array | `ArrayTypeTestCase` |
| Range | `RangeTypeTestCase` |
| Multirange | `MultirangeTypeTestCase` |
| Vector | `VectorTypeTestCase` |
| Spatial array | `SpatialArrayTypeTestCase` |

Reference: `XmlTypeTest`/`XmlArrayTypeTest`, `MoneyTypeTest`, `BitTypeTest`/`BitArrayTypeTest`.

## 7. Update documentation

These are NOT auto-generated — agents must update them manually:
- `docs/AVAILABLE-TYPES.md` — add row to the type catalog table (always)
- `docs/INTEGRATING-WITH-DOCTRINE.md` — add type registration example
- `docs/INTEGRATING-WITH-SYMFONY.md` — add Symfony config entry
- `docs/INTEGRATING-WITH-LARAVEL.md` — add Laravel config entry
- Group-specific doc if one exists (e.g., `docs/RANGE-TYPES.md`, `docs/SPATIAL-TYPES.md`, `docs/GEOMETRY-ARRAYS.md`, `docs/LTREE-TYPE.md`)
- `README.md` — only if this is a brand new feature group not yet mentioned

Read each doc before editing to match the existing format.

## 8. Verify

1. `composer dump-autoload` — ensure autoloader knows about new classes
2. `./bin/phpstan analyse --configuration=ci/phpstan/config.neon <all-new-src-and-test-files> --memory-limit=512M`
3. `./bin/phpunit --filter "{TypeName}" --configuration ci/phpunit/config-unit.xml`

Max 3 attempts before asking for guidance.
