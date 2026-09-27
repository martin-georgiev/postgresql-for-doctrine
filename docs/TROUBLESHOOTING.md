# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Troubleshooting

This page lists the errors people meet most often with this library, by the message you see, with the cause and the fix. The messages are copied from Doctrine DBAL 4.5 and ORM 3.7 on PostgreSQL 18; the platform class, the column number and the parameter number depend on your versions and your query.

> **See also:** [Getting started](GETTING-STARTED.md) · [Writing DQL](DQL-DIALECT.md) · [Query results](HYDRATION.md)

## When Doctrine parses the query

### `Expected known function, got 'ARRAY'`

**Cause:** the query calls a function that is not registered.

**Fix:** register every function the query calls, including helpers such as `ARRAY`, as [Doctrine setup](INTEGRATING-WITH-DOCTRINE.md#register-dql-functions), [Symfony setup](INTEGRATING-WITH-SYMFONY.md#register-dql-functions) or [Laravel setup](INTEGRATING-WITH-LARAVEL.md#register-dql-functions) shows.

### `Expected =, <, <=, <>, >, >=, !=, got end of string.`

The same error can end in `got 'ORDER'`, or in whatever else follows the function.

**Cause:** a function that returns a boolean stands alone in `WHERE` or `HAVING`. DQL accepts a function there only as part of a comparison; [Boolean functions need a comparison](DQL-DIALECT.md#boolean-functions-need-a-comparison) explains the rule.

**Fix:** compare it with `TRUE`:

```sql
WHERE CONTAINS(p.tags, ARRAY('php')) = TRUE
```

### `Expected =, <, <=, <>, >, >=, !=, got 'ILIKE'`

**Cause:** the query uses PostgreSQL's operator syntax, `i.subject ILIKE 'test%'`, and DQL has no such operator. [Function and operator names](DQL-DIALECT.md#function-and-operator-names) lists the DQL name of every operator.

**Fix:** call the function instead:

```sql
WHERE ILIKE(i.subject, 'test%') = TRUE
```

## When the schema tool or a migration runs

### `Unknown database type "text[]" requested`

```text
Unknown database type "text[]" requested, Doctrine\DBAL\Platforms\PostgreSQL120Platform may not support it.
```

**Cause:** Doctrine is writing DDL for a column of this type and cannot find the type's own name on the platform.

**Fix:** register the type's name as a mapping:

```php
$platform->registerDoctrineTypeMapping('text[]', 'text[]');
```

### `Unknown database type "_text" requested`

```text
Unknown database type "_text" requested, Doctrine\DBAL\Platforms\PostgreSQL120Platform may not support it.
```

**Cause:** Doctrine is reading a column back from the database, for `doctrine:schema:update` or a migration diff, and PostgreSQL reports an array column's type with a leading underscore. The schema tools read every table in the database, not only the ones your entities map, unless a schema filter limits them, so an unmapped array column anywhere triggers it.

**Fix:** map that name too:

```php
$platform->registerDoctrineTypeMapping('_text', 'text[]');
```

Your own [enum](ENUM-TYPE.md#4-register-the-type) and [composite](COMPOSITE-TYPE.md#3-register-the-type) types need mappings too; their pages list them.

### `Type "jsonb" already exists.`

**Cause:** DBAL 4.3 and later [ship their own `jsonb` type](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/types.html#jsonb).

**Fix:** replace it with this library's:

```php
Type::overrideType('jsonb', Jsonb::class);
```

## When PostgreSQL runs the query

### `type "ltree" does not exist`

The same cause shows up for a function as `function similarity(text, unknown) does not exist`.

**Cause:** the PostgreSQL extension behind the type or function is not installed in this database.

**Fix:** install it once per database, for example in a migration:

```sql
CREATE EXTENSION IF NOT EXISTS ltree;
```

| Extension | Needed for |
|---|---|
| `postgis` | `geometry`, `geography` and the `ST_*` functions |
| `ltree` | `ltree`, `lquery`, `ltxtquery` and their functions |
| `hstore` | `hstore` and its functions |
| `citext` | `citext` |
| `cube` | `cube` |
| `vector` ([pgvector](https://github.com/pgvector/pgvector)) | `vector`, `halfvec`, `sparsevec` and the distance functions |
| `ulid` ([pgx_ulid](https://github.com/pksunkara/pgx_ulid)) | `ulid` |
| `pg_trgm` | the similarity functions and operators |
| `fuzzystrmatch` | `LEVENSHTEIN`, `SOUNDEX` and the other fuzzy matching functions |
| `unaccent` | `UNACCENT` |
| `earthdistance` | `DISTANCE`; `CREATE EXTENSION earthdistance CASCADE` also installs `cube` |

### `operator does not exist: integer[] @> text[]`

**Cause:** `ARRAY('1', '2')` builds a `text[]`, and PostgreSQL will not compare it with an `integer[]` column.

**Fix:** pass a PostgreSQL array literal instead, which takes the column's type:

```sql
WHERE CONTAINS(i.numbers, '{1,2}') = TRUE
```

### `could not determine data type of parameter $1`

**Cause:** a function such as `JSONB_BUILD_OBJECT` accepts any type, so PostgreSQL cannot tell what a bare parameter is.

**Fix:** cast the parameter:

```sql
JSONB_BUILD_OBJECT('limit', CAST(:limit AS INTEGER))
```

## When Doctrine hydrates the result

### `Cannot assign int to property … of type ?array`

```text
Cannot assign int to property App\Entity\Item::$attributes of type ?array
```

**Cause:** that row's `jsonb` value is a plain number, not an object or an array.

**Fix:** type the property `mixed`; see [jsonb](HYDRATION.md#jsonb).

### A computed value arrives as a string

For example `'{books,php,new}'` instead of a PHP array.

**Cause:** Doctrine converts a mapped field with its type, but hands a value the query computes over as PostgreSQL's text.

**Fix:** convert it yourself, as [Converting a computed value yourself](HYDRATION.md#converting-a-computed-value-yourself) shows.

### `getSingleScalarResult()` returns `'{php,postgres}'` for an array field

**Cause:** the scalar result methods skip the DBAL type, even for a mapped field.

**Fix:** use `getSingleResult()` and take the column from the row; see [Query results](HYDRATION.md#in-short).
