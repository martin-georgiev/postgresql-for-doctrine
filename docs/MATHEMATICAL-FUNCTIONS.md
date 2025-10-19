# Mathematical and Utility Functions

This document covers PostgreSQL mathematical, utility, and miscellaneous functions available in this library.

> 📖 **See also**: [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md) for practical mathematical function examples

## Mathematical Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| cbrt | CBRT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt` |
| ceil | CEIL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil` |
| degrees | DEGREES | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees` |
| exp | EXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp` |
| floor | FLOOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor` |
| greatest | GREATEST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest` |
| least | LEAST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least` |
| ln | LN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln` |
| log | LOG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log` |
| pi | PI | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi` |
| power | POWER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Power` |
| radians | RADIANS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians` |
| random | RANDOM | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random` |
| round | ROUND | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Round` |
| sign | SIGN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign` |
| trunc | TRUNC | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trunc` |
| width_bucket | WIDTH_BUCKET | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket` |

## Type Conversion and Formatting Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| cast | CAST | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast` |
| to_char | TO_CHAR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToChar` |
| to_number | TO_NUMBER | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber` |

## Utility and Miscellaneous Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| any_value | ANY_VALUE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue` |
| row | ROW | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row` |
| row_to_json | ROW_TO_JSON | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson` |
| xmlagg | XML_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg` |

## Usage Examples

```sql
-- Basic mathematical operations
-- Calculate square root using power
SELECT e, POWER(e.value, 0.5) as square_root FROM Entity e WHERE e.value > 0

-- Calculate cube root
SELECT e, CBRT(e.volume) as side_length FROM Entity e

-- Round values
SELECT e, ROUND(e.price, 2) as rounded_price FROM Entity e

-- Ceiling and floor
SELECT e, CEIL(e.rating) as rating_ceiling, FLOOR(e.rating) as rating_floor FROM Entity e

-- Truncate decimal places
SELECT e, TRUNC(e.value, 2) as truncated_value FROM Entity e

-- Find maximum and minimum values
SELECT e, GREATEST(e.value1, e.value2, e.value3) as max_value,
       LEAST(e.value1, e.value2, e.value3) as min_value FROM Entity e

-- Logarithmic functions
SELECT e, LN(e.value) as natural_log, LOG(10, e.value) as log_base_10 FROM Entity e
WHERE e.value > 0

-- Exponential function
SELECT e, EXP(e.exponent) as exponential_value FROM Entity e

-- Trigonometric conversions
SELECT e, DEGREES(e.radians) as degrees, RADIANS(e.degrees) as radians FROM Entity e

-- Sign function
SELECT e, SIGN(e.balance) as balance_sign FROM Entity e

-- Random values
SELECT e, RANDOM() as random_value FROM Entity e

-- Pi constant
SELECT e, PI() * e.radius * e.radius as circle_area FROM Entity e

-- Width bucket for histograms
SELECT WIDTH_BUCKET(e.score, 0, 100, 10) as bucket, COUNT(*) as count
FROM Entity e GROUP BY bucket ORDER BY bucket

-- Type conversion and formatting
-- Cast values to different types
SELECT e, CAST(e.text_number as INTEGER) as number_value FROM Entity e

-- Format numbers as text
SELECT e, TO_CHAR(e.amount, '999,999.99') as formatted_amount FROM Entity e

-- Parse text as numbers
SELECT e, TO_NUMBER(e.price_text, '999.99') as price_number FROM Entity e

-- Format dates
SELECT e, TO_CHAR(e.created_at, 'YYYY-MM-DD HH24:MI:SS') as formatted_date FROM Entity e

-- Utility functions
-- Get any value from a group (useful for aggregation)
SELECT e.category, ANY_VALUE(e.description) as sample_description
FROM Entity e GROUP BY e.category

-- Create row values
SELECT e, ROW(e.x, e.y, e.z) as coordinates FROM Entity e

-- Convert row to JSON
SELECT e, ROW_TO_JSON(ROW(e.name, e.value, e.category)) as entity_json FROM Entity e

-- XML aggregation
SELECT e.category, XML_AGG(e.name) as names_xml FROM Entity e GROUP BY e.category

-- Complex mathematical calculations
-- Calculate compound interest
SELECT e, e.principal * POWER(1 + e.rate, e.years) as compound_amount FROM Entity e

-- Calculate distance using Pythagorean theorem
SELECT e, POWER(POWER(e.x2 - e.x1, 2) + POWER(e.y2 - e.y1, 2), 0.5) as distance FROM Entity e

-- Normalize values to 0-1 range
SELECT e, (e.value - e.min_value) / (e.max_value - e.min_value) as normalized FROM Entity e
WHERE e.max_value != e.min_value

-- Calculate percentiles using width_bucket
SELECT e.score,
       WIDTH_BUCKET(e.score, 0, 100, 100) as percentile
FROM Entity e ORDER BY e.score

-- Statistical calculations
SELECT e.category,
       COUNT(*) as count,
       ROUND(AVG(e.value), 2) as avg_value,
       GREATEST(MAX(e.value), 0) as max_value,
       LEAST(MIN(e.value), 0) as min_value
FROM Entity e GROUP BY e.category

-- Random sampling
SELECT e FROM Entity e WHERE RANDOM() < 0.1 ORDER BY RANDOM() LIMIT 100

-- Bucketing for analytics
SELECT WIDTH_BUCKET(e.age, 0, 100, 10) as age_bucket,
       COUNT(*) as count,
       ROUND(AVG(e.income), 2) as avg_income
FROM Entity e 
GROUP BY age_bucket 
ORDER BY age_bucket

-- Format currency
SELECT e, TO_CHAR(e.price, 'L999,999.99') as formatted_price FROM Entity e

-- Parse formatted numbers
SELECT e, TO_NUMBER(e.formatted_value, '999,999.99') as parsed_value FROM Entity e
```

**📝 Function Categories:**

### **Mathematical Functions**
- **Basic Math**: CEIL, FLOOR, ROUND, TRUNC for rounding operations
- **Power Functions**: POWER, CBRT, EXP for exponential calculations
- **Logarithmic**: LN, LOG for logarithmic operations
- **Trigonometric**: DEGREES, RADIANS for angle conversions
- **Comparison**: GREATEST, LEAST for finding extremes
- **Utility**: SIGN, RANDOM, PI for various mathematical needs

### **Statistical Functions**
- **WIDTH_BUCKET**: Creates histogram buckets for data analysis
- **ANY_VALUE**: Returns any value from a group (useful with GROUP BY)

### **Formatting Functions**
- **TO_CHAR**: Converts numbers and dates to formatted strings
- **TO_NUMBER**: Parses formatted text as numbers
- **CAST**: General type conversion

### **Data Structure Functions**
- **ROW**: Creates row values
- **ROW_TO_JSON**: Converts rows to JSON format
- **XML_AGG**: Aggregates values into XML format

**💡 Tips for Usage:**
1. **Mathematical functions** work with numeric types and return appropriate precision
2. **Formatting functions** support PostgreSQL's rich formatting patterns
3. **WIDTH_BUCKET** is excellent for creating histograms and analytics
4. **RANDOM()** generates values between 0 and 1
5. **GREATEST/LEAST** can take multiple arguments and handle NULL values
6. **Type conversion** with CAST supports all PostgreSQL data types
7. **Logarithmic functions** require positive input values
