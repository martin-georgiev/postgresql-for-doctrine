# Mathematical Functions

This document covers PostgreSQL mathematical functions available in this library.

> 📖 **See also**: [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md) for practical mathematical function examples

## Trigonometric Functions

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

## Mathematical Functions

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

## Usage Examples

```sql
-- WIDTH_BUCKET: bucket number (1-based) for a value in a histogram with N equal-width buckets
SELECT WIDTH_BUCKET(e.score, 0, 100, 10) as bucket, COUNT(*) as count
FROM Entity e GROUP BY bucket ORDER BY bucket

-- POWER used for square root and Pythagorean distance (no SQRT() in DQL)
SELECT POWER(e.value, 0.5) as square_root FROM Entity e WHERE e.value > 0
SELECT POWER(POWER(e.x2 - e.x1, 2) + POWER(e.y2 - e.y1, 2), 0.5) as distance FROM Entity e

-- Random reservoir sampling: WHERE filters ~10% of rows, ORDER BY shuffles them
SELECT e FROM Entity e WHERE RANDOM() < 0.1 ORDER BY RANDOM() LIMIT 100

-- GREATEST/LEAST with aggregates — clamp aggregate results to a floor or ceiling
SELECT e.category,
       GREATEST(MAX(e.value), 0) as max_non_negative,
       LEAST(MIN(e.value), 100) as min_capped
FROM Entity e GROUP BY e.category
```
**📝 Function Categories:**

### **Mathematical Functions**
- **Basic Math**: CEIL, FLOOR, ROUND, TRUNC for rounding operations
- **Power Functions**: POWER, CBRT, EXP for exponential calculations
- **Logarithmic**: LN, LOG for logarithmic operations
- **Trigonometric**: DEGREES, RADIANS for angle conversions
- **Comparison**: GREATEST, LEAST for finding extremes
- **Utility**: SIGN, RANDOM, PI for various mathematical needs

**💡 Tips for Usage:**
1. **Mathematical functions** work with numeric types and return appropriate precision
2. **WIDTH_BUCKET** is excellent for creating histograms and analytics
3. **RANDOM()** generates values between 0 and 1
4. **GREATEST/LEAST** can take multiple arguments and handle NULL values
5. **Logarithmic functions** require positive input values
