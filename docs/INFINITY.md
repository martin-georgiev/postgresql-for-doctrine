# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Infinity Values

PostgreSQL has infinity in several type families, and this library gives it three different PHP shapes. The shape is not a style choice — it follows from what `null` already means in that position.

## Floats: PHP's `INF` and `NAN`

Items of `real[]` and `double precision[]`, `Cube` coordinates and the geometric value objects use PHP's native constants. PHP has a representation of its own, so nothing needs to be invented.

```php
[\INF, -\INF, \NAN, 1.5];  // double precision[] -> {Infinity,-Infinity,NaN,1.5}
new Point(\INF, 2.0);      // point -> (Infinity,2)
```

## Range bounds: boolean flags

A range bound is marked with `isLowerBoundedInfinity()` / `isUpperBoundedInfinity()`, kept distinct from a `null` bound, because PostgreSQL keeps them distinct too:

```sql
SELECT '[-infinity,infinity)'::daterange = '(,)'::daterange; -- false
SELECT lower('(,)'::daterange) IS NULL;                      -- true
SELECT lower('[-infinity,infinity)'::daterange);             -- -infinity
```

`null` already carries "this end is unbounded", so a bound of infinity needs a third state next to it — hence the flag. See [Range Types](RANGE-TYPES.md#infinity-support), which also covers the `NumericRange(0, INF)` shorthand.

## Array elements: an enum sentinel

Items of `date[]`, `timestamp[]` and `timestamptz[]` read back as `\DateTimeImmutable`, which cannot hold infinity, and `null` is already taken by SQL NULL. They map to the `DateTimeInfinity` enum instead:

```php
[new \DateTimeImmutable('2024-01-01'), DateTimeInfinity::POSITIVE, null];
// date[] -> {"2024-01-01","infinity",NULL}
```

See [Datetime Array Types](AVAILABLE-TYPES.md#datetime-array-types).

Forcing the three into one shape would make two of them worse: floats would carry a wrapper they do not need, and ranges and arrays would both lose the distinction between infinity and `null`.

## Spellings differ by type family

The input grammar is not the same across families, which is why the library recognizes two sets of tokens rather than one:

| Input | `date`, `timestamp` | `float8`, `numeric` |
|---|---|---|
| `infinity`, `-infinity` | accepted | accepted |
| `inf`, `-inf` | rejected | accepted |
| `nan` | rejected | accepted |

On output the datetime types print `infinity` in lowercase, while the numeric ones print `Infinity` and `NaN` capitalized. Each family is parsed with the grammar it actually uses.
