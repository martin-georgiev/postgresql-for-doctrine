# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Mathematical and statistical functions

This page covers PostgreSQL [mathematical functions](https://www.postgresql.org/docs/18/functions-math.html) available in this library.

> **See also:** [Examples](USE-CASES-AND-EXAMPLES.md) for these functions in whole queries

## Trigonometric functions

### Radian-based functions

| PostgreSQL function | Register for DQL as | Implemented by |
|---|---|---|
| sin | SIN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sin` |
| cos | COS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cos` |
| tan | TAN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tan` |
| cot | COT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cot` |
| asin | ASIN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asin` |
| acos | ACOS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acos` |
| atan | ATAN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan` |
| atan2 | ATAN2 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan2` |
| sinh | SINH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sinh` |
| cosh | COSH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosh` |
| tanh | TANH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tanh` |
| asinh | ASINH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asinh` |
| acosh | ACOSH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosh` |
| atanh | ATANH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atanh` |

### Degree-based functions

| PostgreSQL function | Register for DQL as | Implemented by |
|---|---|---|
| sind | SIND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sind` |
| cosd | COSD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosd` |
| tand | TAND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tand` |
| cotd | COTD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cotd` |
| asind | ASIND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asind` |
| acosd | ACOSD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Acosd` |
| atand | ATAND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atand` |
| atan2d | ATAN2D | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan2d` |

## Mathematical functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| cbrt | CBRT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt` |
| ceil | CEIL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil` |
| degrees | DEGREES | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees` |
| div | DIV | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Div` |
| erf | ERF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erf` |
| erfc | ERFC | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erfc` |
| exp | EXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp` |
| factorial | FACTORIAL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Factorial` |
| floor | FLOOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor` |
| gamma | GAMMA | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Gamma` |
| gcd | GCD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Gcd` |
| generate_series | GENERATE_NUMERIC_SERIES | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenerateNumericSeries` |
| greatest | GREATEST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest` |
| lcm | LCM | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lcm` |
| least | LEAST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least` |
| lgamma | LGAMMA | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lgamma` |
| ln | LN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln` |
| log | LOG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log` |
| min_scale | MIN_SCALE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MinScale` |
| pi | PI | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi` |
| power | POWER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power` |
| radians | RADIANS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians` |
| random | RANDOM | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random` |
| random_normal | RANDOM_NORMAL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RandomNormal` |
| round | ROUND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round` |
| scale | SCALE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Scale` |
| sign | SIGN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign` |
| trim_scale | TRIM_SCALE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TrimScale` |
| trunc | TRUNC | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc` |
| width_bucket | WIDTH_BUCKET | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket` |

## Hashing & cryptographic functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| md5 | MD5 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Md5` |
| sha224 | SHA224 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha224` |
| sha256 | SHA256 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha256` |
| sha384 | SHA384 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha384` |
| sha512 | SHA512 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha512` |

## Bitwise and boolean aggregate functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| bit_and | BIT_AND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitAnd` |
| bit_or | BIT_OR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitOr` |
| bit_xor | BIT_XOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitXor` |
| bool_and | BOOL_AND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BoolAnd` |
| bool_or | BOOL_OR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BoolOr` |
| every | EVERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Every` |

## Statistical aggregate functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| corr | CORR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Corr` |
| covar_pop | COVAR_POP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CovarPop` |
| covar_samp | COVAR_SAMP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CovarSamp` |
| mode() WITHIN GROUP (ORDER BY ...) | MODE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Mode` |
| percentile_cont(fraction) WITHIN GROUP (ORDER BY ...) | PERCENTILE_CONT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileCont` |
| percentile_disc(fraction) WITHIN GROUP (ORDER BY ...) | PERCENTILE_DISC | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileDisc` |
| stddev | STDDEV | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Stddev` |
| stddev_pop | STDDEV_POP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StddevPop` |
| var_pop | VAR_POP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\VarPop` |
| variance | VARIANCE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Variance` |

### `WITHIN GROUP` goes inside the parentheses in DQL

In SQL, an [ordered-set aggregate](https://www.postgresql.org/docs/18/functions-aggregate.html#FUNCTIONS-ORDEREDSET-TABLE) closes its parentheses and then takes `WITHIN GROUP (ORDER BY ...)`. DQL cannot parse anything after a function's closing parenthesis, so in DQL the clause moves **inside** the call:

| SQL | DQL |
|---|---|
| `percentile_cont(0.5) WITHIN GROUP (ORDER BY e.value)` | `PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY e.value)` |
| `percentile_disc(0.9) WITHIN GROUP (ORDER BY e.value DESC)` | `PERCENTILE_DISC(0.9 WITHIN GROUP ORDER BY e.value DESC)` |
| `mode() WITHIN GROUP (ORDER BY e.status)` | `MODE(WITHIN GROUP ORDER BY e.status)` |

- No comma between the fraction and `WITHIN GROUP`, and no parentheses around `ORDER BY ...`. `MODE` takes no fraction, so its call starts straight with `WITHIN GROUP`.
- `ORDER BY` takes exactly one item, optionally with `ASC` / `DESC`. PostgreSQL rejects more than one, and DQL does not accept a literal there.
- The fraction is a literal, a parameter, or an expression over columns listed in `GROUP BY`. PostgreSQL rejects a fraction that reads an ungrouped column.

## Utility and miscellaneous functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| any_value | ANY_VALUE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue` |
| crc32 | CRC32 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32` |
| crc32c | CRC32C | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32c` |
| reverse (bytea) | REVERSE_BYTES | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReverseBytes` |
| row | ROW | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row` |
| row_to_json | ROW_TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson` |
| xmlagg | XMLAGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg` |

## Usage examples

The results come from these rows of `Entity`, where `value` and the coordinates are `double precision`:

| id | category | status | score | value | x1 | y1 | x2 | y2 |
|---|---|---|---|---|---|---|---|---|
| 1 | books | active | 91 | 16 | 0 | 0 | 3 | 4 |
| 2 | books | active | 78 | 49 | 1 | 1 | 4 | 5 |
| 3 | books | draft | 85 | 120 | 0 | 0 | 0 | 2 |
| 4 | music | draft | 42 | -5 | 2 | 2 | 2 | 2 |

Each `-- →` line under a query is one row of its result. A query without its own `ORDER BY` has its rows listed in sample-row order, or by group.

```sql
-- WIDTH_BUCKET: bucket number (1-based) for a value in a histogram with N equal-width buckets
SELECT WIDTH_BUCKET(e.score, 0, 100, 10) as bucket, COUNT(e.id) as count
FROM Entity e GROUP BY bucket ORDER BY bucket
-- → ['bucket' => 5, 'count' => 1]
--   ['bucket' => 8, 'count' => 1]
--   ['bucket' => 9, 'count' => 1]
--   ['bucket' => 10, 'count' => 1]

-- POWER used for square root and Pythagorean distance
SELECT POWER(e.value, 0.5) as square_root FROM Entity e WHERE e.value > 0
-- → ['square_root' => 4.0]
--   ['square_root' => 7.0]
--   ['square_root' => 10.954451150103322]
SELECT POWER(POWER(e.x2 - e.x1, 2) + POWER(e.y2 - e.y1, 2), 0.5) as distance FROM Entity e
-- → ['distance' => 5.0]
--   ['distance' => 5.0]
--   ['distance' => 2.0]
--   ['distance' => 0.0]

-- Random reservoir sampling: WHERE filters ~10% of rows, ORDER BY shuffles them
SELECT e FROM Entity e WHERE RANDOM() < 0.1 ORDER BY RANDOM() -- DQL has no LIMIT: cap the rows with $query->setMaxResults(100)
-- → each row with a one-in-ten chance, in random order

-- GREATEST/LEAST with aggregates - clamp aggregate results to a floor or ceiling
SELECT e.category,
       GREATEST(MAX(e.value), 0) as max_non_negative,
       LEAST(MIN(e.value), 100) as min_capped
FROM Entity e GROUP BY e.category
-- → ['category' => 'books', 'max_non_negative' => 120.0, 'min_capped' => 16.0]
--   ['category' => 'music', 'max_non_negative' => 0.0, 'min_capped' => -5.0]

-- Ordered-set aggregates: WITHIN GROUP ORDER BY goes inside the parentheses (see above)
SELECT e.category,
       PERCENTILE_CONT(0.5 WITHIN GROUP ORDER BY e.value) as median,
       PERCENTILE_DISC(0.9 WITHIN GROUP ORDER BY e.value) as top_decile,
       MODE(WITHIN GROUP ORDER BY e.status) as most_common_status
FROM Entity e GROUP BY e.category
-- → ['category' => 'books', 'median' => 49.0, 'top_decile' => 120.0, 'most_common_status' => 'active']
--   ['category' => 'music', 'median' => -5.0, 'top_decile' => -5.0, 'most_common_status' => 'draft']
```
**Function Categories:**

### Mathematical functions
- **Basic Math**: CEIL, FLOOR, ROUND, TRUNC for rounding operations
- **Power Functions**: POWER, CBRT, EXP for exponential calculations
- **Logarithmic**: LN, LOG for logarithmic operations
- **Trigonometric**: DEGREES, RADIANS for angle conversions
- **Comparison**: GREATEST, LEAST for finding extremes
- **Utility**: SIGN, RANDOM, PI for various mathematical needs

**Tips:**
- `WIDTH_BUCKET` numbers the equal-width bucket a value falls into, which is what a histogram groups by.
- `RANDOM()` returns a value from 0 up to, but not including, 1.
- `GREATEST` and `LEAST` skip `NULL` arguments; they return `NULL` only when every argument is `NULL`.
- `LN` and `LOG` fail on zero and on negative numbers.
