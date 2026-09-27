# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Available functions and operators

This page lists the PostgreSQL functions and operators available in this library. Each category below links to its own page with the details.

> **See also:** [Query results](QUERY-RESULTS.md) for the PHP value a function result arrives as

## Operator conflicts and usage notes

Some PostgreSQL operators have multiple meanings depending on the data types involved. This library provides specific DQL function names to avoid conflicts:

| Operator | Array/JSON usage | Spatial usage | Text/pattern usage |
|---|---|---|---|
| `@>` | `CONTAINS` (arrays contain elements) | N/A (PostGIS defines no `@>`; see `~`) | N/A |
| `<@` | `IS_CONTAINED_BY` (element in array) | N/A (PostGIS defines no `<@`; see `@`) | N/A |
| `@` | N/A | `SPATIAL_CONTAINED_BY` (bounding box contained) | N/A |
| `~` | N/A | `SPATIAL_CONTAINS` (bounding box contains) | `REGEXP` (text pattern matching) |
| `&&` | `OVERLAPS` (arrays/ranges overlap) | Works automatically with geometry/geography | N/A |

**Usage Guidelines:**
- **Arrays/JSON**: Use `CONTAINS`, `IS_CONTAINED_BY`, `OVERLAPS` for array and JSON operations → [Array and JSON functions](ARRAY-AND-JSON-FUNCTIONS.md)
- **Spatial**: Use `SPATIAL_CONTAINS`, `SPATIAL_CONTAINED_BY` for explicit spatial bounding box operations → [PostGIS spatial functions](SPATIAL-FUNCTIONS-AND-OPERATORS.md)
- **Text**: Use `REGEXP`, `IREGEXP` for pattern matching → [Text and pattern functions](TEXT-AND-PATTERN-FUNCTIONS.md)
- **Boolean operators**: the containment, overlap and bounding box operators return booleans and **must be compared with `= TRUE` or `= FALSE` in DQL**
- **The rules behind these names**, and the ones every function follows (`= TRUE`, `FILTER` and `OVER`, literals and parameters): [Writing DQL](WRITING-DQL.md)

## Function and operator categories

Each category has its own page:

### Array and JSON functions
[Array and JSON functions and operators](ARRAY-AND-JSON-FUNCTIONS.md): the array operators (`@>`, `<@`, `&&`), the JSON operators (`->`, `->>`, `#>`, `#>>`), and the array, JSON, JSONB and JSON path functions.

### PostGIS spatial functions
[PostGIS spatial functions and operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md): the bounding box and distance operators, and the accessor, constructor, relationship, measurement, overlay, processing, editor and linear referencing functions.

### Text and pattern functions
[Text and pattern functions and operators](TEXT-AND-PATTERN-FUNCTIONS.md): the text operators (`~`, `ilike`, `@@`), regular expressions, text processing, full-text search, fuzzy string matching, and hashing and checksums.

### Date and range functions
[Date, time, and range functions](DATE-AND-RANGE-FUNCTIONS.md): date and time arithmetic and extraction, range constructors, and the range operators.

### Mathematical functions
[Mathematical and statistical functions](MATHEMATICAL-FUNCTIONS.md): arithmetic, rounding, trigonometry, random numbers, the bitwise and boolean aggregates (`BIT_AND`, `BOOL_OR`, `EVERY`, …) and the statistical aggregates (`CORR`, `STDDEV`, `PERCENTILE_CONT`, `MODE`, …).

### Utility functions
[Utility functions](UTILITY-FUNCTIONS.md): type casting (`CAST`), formatting (`TO_CHAR`, `TO_NUMBER`), and UUID generation and inspection (`GEN_RANDOM_UUID`, `UUIDV4`, `UUIDV7`, `UUID_EXTRACT_TIMESTAMP`, `UUID_EXTRACT_VERSION`).

### XML functions
[XML functions](XML-FUNCTIONS.md): aggregation (`XMLAGG`), well-formedness checks (`XML_IS_WELL_FORMED`, `XML_IS_WELL_FORMED_DOCUMENT`, `XML_IS_WELL_FORMED_CONTENT`), construction (`XMLTEXT`, `XMLCOMMENT`, `XMLCONCAT`, `XMLPI`) and XPath (`XPATH`, `XPATH_EXISTS`, `XMLEXISTS`).

### Window functions
[Window functions](WINDOW-FUNCTIONS.md): `OVER` for running any aggregate over a window, with frame clauses, the ranking functions `ROW_NUMBER`, `RANK`, `DENSE_RANK`, `PERCENT_RANK`, `CUME_DIST` and `NTILE`, and the value functions `LAG`, `LEAD`, `FIRST_VALUE`, `LAST_VALUE` and `NTH_VALUE`.

### Network address functions
[Network address functions](NETWORK-FUNCTIONS.md): taking an `inet` or `cidr` value apart (`HOST`, `BROADCAST`, `NETWORK`, `NETMASK`, `HOSTMASK`), reading it (`FAMILY`, `MASKLEN`, `ABBREV`), combining two (`INET_MERGE`, `INET_SAME_FAMILY`) and changing the mask (`SET_MASKLEN`).

### Hstore functions (requires [hstore](https://www.postgresql.org/docs/18/hstore.html) extension)
[Hstore functions](HSTORE-FUNCTIONS.md): keys and values as arrays or sets (`HSTORE_AKEYS`, `HSTORE_SKEYS`, `HSTORE_AVALS`, `HSTORE_SVALS`), `HSTORE_DEFINED`, `HSTORE_DELETE`, and conversion to JSON (`HSTORE_TO_JSON`, `HSTORE_TO_JSON_LOOSE`).

### Ltree functions
[PostgreSQL ltree types](LTREE-TYPE.md): extracting subpaths, path length and position, the longest common ancestor, conversion to and from text, and the `lquery` and `ltxtquery` match operators.

### Vector distance functions (requires [pgvector](https://github.com/pgvector/pgvector) extension)
Distance functions for fixed-dimension float vectors stored with the `vector` type:
- `L2_DISTANCE` - Euclidean (L2) distance between two vectors
- `COSINE_DISTANCE` - Cosine distance between two vectors
- `INNER_PRODUCT` - Inner (dot) product of two vectors

## Quick reference

### Most commonly used functions

**Array Operations:** ([full list](ARRAY-AND-JSON-FUNCTIONS.md))
- `CONTAINS` (`@>`) - Test if array/range contains elements
- `OVERLAPS` (`&&`) - Test if arrays/ranges overlap
- `ARRAY_AGG` - Aggregate values into arrays
- `FILTER` - Restrict the rows an aggregate reads (`FILTER (WHERE ...)`)

**JSON Operations:** ([full list](ARRAY-AND-JSON-FUNCTIONS.md))
- `JSON_GET_FIELD_AS_TEXT` (`->>`) - Extract JSON field as text
- `JSON_BUILD_OBJECT` - Build JSON objects
- `JSONB_PATH_EXISTS` - Test JSON path existence

**Spatial Operations:** ([full list](SPATIAL-FUNCTIONS-AND-OPERATORS.md))
- `ST_INTERSECTS` - Test if geometries intersect
- `ST_DISTANCE` - Calculate distance between geometries
- `ST_CONTAINS` - Test spatial containment

**Text Operations:** ([full list](TEXT-AND-PATTERN-FUNCTIONS.md))
- `ASCII` - Get numeric code of first character
- `BTRIM`/`LTRIM`/`RTRIM` - Trim characters from string ends
- `CASEFOLD` - Fold case for a case-insensitive comparison that follows Unicode (PostgreSQL 18)
- `CHR` - Get character from code point
- `ILIKE` - Case-insensitive pattern matching
- `INITCAP` - Capitalize first letter of each word
- `LPAD`/`RPAD` - Left/right pad a string to a given length
- `QUOTE_IDENT`/`QUOTE_LITERAL`/`QUOTE_NULLABLE` - Quote SQL identifiers and literals
- `REGEXP` (`~`) - Regular expression matching
- `STARTS_WITH` - Test if text starts with a substring
- `STRPOS` - Find position of substring
- `TRANSLATE` - Replace characters in a string
- `LEVENSHTEIN` - Calculate edit distance between strings (fuzzy matching)
- `SOUNDEX` - Phonetic encoding for similarity matching
- `MD5`/`SHA256`/`SHA512` (and other SHA variants) - Cryptographic hashing
- `CRC32`/`CRC32C` - CRC checksum computation
- `REVERSE_BYTES` - Reverse byte order for bytea values

**Date/Range Operations:** ([full list](DATE-AND-RANGE-FUNCTIONS.md))
- `CLOCK_TIMESTAMP` - Current timestamp at call time
- `DATE_ADD` - Add interval to date
- `DATE_EXTRACT` - Extract date components
- `DATERANGE` - Create date ranges
- `ISFINITE` - Test if date/timestamp/interval is finite
- `JUSTIFY_DAYS`/`JUSTIFY_HOURS`/`JUSTIFY_INTERVAL` - Adjust interval representations
- `STATEMENT_TIMESTAMP` - Timestamp of current SQL statement
- `TRANSACTION_TIMESTAMP` - Timestamp of current transaction

**Mathematical Operations:** ([full list](MATHEMATICAL-FUNCTIONS.md))
- `DIV`/`GCD`/`LCM`/`FACTORIAL` - Integer arithmetic functions
- `ERF`/`ERFC` - Error and complementary error functions
- `GAMMA`/`LGAMMA` - Gamma function for statistical calculations
- `GREATEST`/`LEAST` - Find maximum/minimum values
- `ROUND`/`SCALE`/`MIN_SCALE`/`TRIM_SCALE` - Numeric precision functions
- `RANDOM`/`RANDOM_NORMAL` - Generate random numbers
- **Bitwise/Boolean Aggregates**: `BIT_AND`, `BIT_OR`, `BIT_XOR`, `BOOL_AND`, `BOOL_OR`, `EVERY`
- **Statistical Aggregates**: `CORR`, `COVAR_POP`, `COVAR_SAMP`, `MODE`, `PERCENTILE_CONT`, `PERCENTILE_DISC`, `STDDEV`, `STDDEV_POP`, `VAR_POP`, `VARIANCE`

**Utility Functions:** ([full list](UTILITY-FUNCTIONS.md))
- `CAST` - General type conversion
- `TO_CHAR` - Convert numbers and dates to formatted strings
- `TO_NUMBER` - Parse formatted text as numbers
- `GEN_RANDOM_UUID` - Generate a random UUID (version 4)
- `UUIDV4` - Explicit UUID version 4 generation
- `UUIDV7` - Generate a UUID (version 7) that sorts by creation time
- `UUID_EXTRACT_TIMESTAMP` - Extract timestamp from UUID v1 or v7
- `UUID_EXTRACT_VERSION` - Extract version number from UUID

**Network Address Operations:** ([full list](NETWORK-FUNCTIONS.md))
- `HOST`/`NETWORK`/`BROADCAST` - Extract address parts
- `MASKLEN`/`NETMASK`/`HOSTMASK` - Mask information
- `INET_MERGE` - Smallest network containing two addresses

**Hstore Operations:** ([full list](HSTORE-FUNCTIONS.md))
- `HSTORE_AKEYS` - Return hstore keys as an array
- `HSTORE_AVALS` - Return hstore values as an array
- `HSTORE_DEFINED` - Check if key exists and is not NULL
- `HSTORE_DELETE` - Delete key from hstore

**Ltree Operations:** ([full list](LTREE-TYPE.md))
- `SUBLTREE` - Extract subpath from ltree
- `SUBPATH` - Extract subpath with offset and length
- `NLEVEL` - Get number of labels in path
- `INDEX` - Find position of ltree in another ltree
- `LCA` - Find longest common ancestor
- `TEXT2LTREE` - Cast text to ltree
- `LTREE2TEXT` - Cast ltree to text
- `MATCHES_LQUERY` - Check whether a path matches an `lquery` pattern (`~`)
- `MATCHES_ANY_LQUERY` - Check whether a path matches any `lquery` pattern in an array (`?`)
- `MATCHES_LTXTQUERY` - Check whether a path matches an `ltxtquery` label query (`@`)

**Vector Distance Operations:**
- `L2_DISTANCE` - Euclidean distance between vectors
- `COSINE_DISTANCE` - Cosine distance between vectors
- `INNER_PRODUCT` - Inner product of two vectors

**Composite Types:**
- `COMPOSITE_FIELD` - Access a field from a PostgreSQL composite type column → [Examples](USE-CASES-AND-EXAMPLES.md#using-postgresql-composite-types)

**XML Functions:** ([full list](XML-FUNCTIONS.md))
- `XML_IS_WELL_FORMED` - Check whether a text string is well-formed XML
- `XML_IS_WELL_FORMED_CONTENT` - Check whether a text string is well-formed XML content
- `XML_IS_WELL_FORMED_DOCUMENT` - Check whether a text string is a well-formed XML document
- `XMLAGG` - Aggregate XML values (supports `ORDER BY`)
- `XMLCOMMENT` - Create an XML comment
- `XMLCONCAT` - Concatenate multiple XML values into a single XML value
- `XMLEXISTS` - Test if an XPath expression matches any nodes in an XML value
- `XMLPI` - Create an XML processing instruction
- `XMLTEXT` - Create an XML text node (with entity escaping)
- `XPATH` - Evaluate an XPath expression against an XML value, returning matched nodes
- `XPATH_EXISTS` - Test if an XPath expression matches any node in an XML value

**Window Functions:** ([full list](WINDOW-FUNCTIONS.md))
- `OVER` - Run an aggregate or a window function over a window, such as running totals and moving averages
- `ROW_NUMBER`/`RANK`/`DENSE_RANK` - Number or rank rows within their partition
- `PERCENT_RANK`/`CUME_DIST` - Relative rank and cumulative distribution within the partition
- `NTILE` - Split the partition into a number of buckets
- `LAG`/`LEAD` - Value from a row before or after the current one
- `FIRST_VALUE`/`LAST_VALUE`/`NTH_VALUE` - Value from the first, last or n-th row of the window frame

## Related pages

- [Available types](AVAILABLE-TYPES.md): the PostgreSQL types this library maps
- [PostgreSQL range types](RANGE-TYPES.md): the range value objects
- [Examples](USE-CASES-AND-EXAMPLES.md): the functions in whole queries
- [PostGIS geometry and geography](POSTGIS-GEOMETRY-AND-GEOGRAPHY.md): the `geometry` and `geography` value object
- [Geometry and geography arrays](GEOMETRY-ARRAYS.md): arrays of spatial values
