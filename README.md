<p align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="docs/assets/logo-dark.svg">
    <img src="docs/assets/logo.svg" alt="" width="96" height="96">
  </picture>
</p>

<h1 align="center">PostgreSQL for Doctrine</h1>

<p align="center">Enhances Doctrine with PostgreSQL-specific features and functions. Supports PostgreSQL 9.4+ and PHP 8.2+.</p>

<p align="center">
  <a href="https://coveralls.io/github/martin-georgiev/postgresql-for-doctrine?branch=main"><img src="https://coveralls.io/repos/github/martin-georgiev/postgresql-for-doctrine/badge.svg?branch=main" alt="Coverage Status"></a>
  <a href="https://packagist.org/packages/martin-georgiev/postgresql-for-doctrine"><img src="https://poser.pugx.org/martin-georgiev/postgresql-for-doctrine/version" alt="Latest Stable Version"></a>
  <a href="https://packagist.org/packages/martin-georgiev/postgresql-for-doctrine"><img src="https://poser.pugx.org/martin-georgiev/postgresql-for-doctrine/downloads" alt="Total Downloads"></a>
</p>

## Quick Start

```php
use Doctrine\DBAL\Types\Type as DoctrineType;
use MartinGeorgiev\Doctrine\DBAL\Type;

// Register types with Doctrine
DoctrineType::addType('jsonb', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Jsonb");
DoctrineType::addType('text[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TextArray");
DoctrineType::addType('numrange', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\NumRange");

// Use in your Doctrine entities
#[ORM\Column(type: Type::JSONB)]
private array $data;

#[ORM\Column(type: Type::TEXT_ARRAY)]
private array $tags;

#[ORM\Column(type: Type::NUMRANGE)]
private NumericRange $priceRange;

// Use in DQL
$query = $em->createQuery('
    SELECT e
    FROM App\Entity\Post e
    WHERE CONTAINS(e.tags, ARRAY(:tags)) = TRUE
    AND JSON_GET_FIELD(e.data, :field) = :value
');
```

## 🚀 Features Highlight

### Data Types
- **Array Types**
  - Integer arrays (`int[]`, `smallint[]`, `bigint[]`)
  - Float arrays (`real[]`, `double precision[]`)
  - Text arrays (`text[]`)
  - Boolean arrays (`bool[]`)
  - JSONB arrays (`jsonb[]`)
- **Binary Types**
  - Raw binary data (`bytea`, `bytea[]`)
- **Bit String Types**
  - Fixed-length bit strings (`bit`, `bit[]`)
  - Variable-length bit strings (`bit varying`, `bit varying[]`)
- **JSON Types**
  - Native JSONB support
  - JSON field operations
  - JSON construction and manipulation
- **Network Types**
  - IP addresses (`inet`, `inet[]`)
  - Network CIDR notation (`cidr`, `cidr[]`)
  - MAC addresses (`macaddr`, `macaddr[]`, `macaddr8`, `macaddr8[]`)
- **Geometric Types**
  - Box (`box`, `box[]`)
  - Circle (`circle`, `circle[]`)
  - Line (`line`, `line[]`)
  - Line segment (`lseg`, `lseg[]`)
  - Path (`path`, `path[]`)
  - Point (`point`, `point[]`)
  - Polygon (`polygon`, `polygon[]`)
  - PostGIS Geometry (`geometry`, `geometry[]`)
  - PostGIS Geography (`geography`, `geography[]`)
- **Range Types**
  - Date and time ranges (`daterange`, `daterange[]`, `tsrange`, `tsrange[]`, `tstzrange`, `tstzrange[]`)
  - Numeric ranges (`numrange`, `numrange[]`, `int4range`, `int4range[]`, `int8range`, `int8range[]`)
  - Multiranges (`datemultirange`, `datemultirange[]`, `int4multirange`, `int4multirange[]`, `int8multirange`, `int8multirange[]`, `nummultirange`, `nummultirange[]`, `tsmultirange`, `tsmultirange[]`, `tstzmultirange`, `tstzmultirange[]`)
- **Date and Time Types**
  - Arrays (`date[]`, `timestamp[]`, `timestamptz[]`)
  - Time durations with `DateInterval` support (`interval`, `interval[]`)
  - String-represented time with timezone (`timetz`, `timetz[]`)
- **Text Search Types**
  - Full-text search document (`tsvector`, `tsvector[]`)
  - Full-text search query (`tsquery`, `tsquery[]`)
- **Case-Insensitive Text Types** (requires [citext](https://www.postgresql.org/docs/18/citext.html) extension)
  - Case-insensitive text (`citext`, `citext[]`)
- **ULID Types** (requires [pgx_ulid](https://github.com/pksunkara/pgx_ulid) extension)
  - Sortable, timestamp-prefixed identifiers (`ulid`, `ulid[]`)
- **Key-Value Types** (requires [hstore](https://www.postgresql.org/docs/18/hstore.html) extension)
  - Key-value store (`hstore`, `hstore[]`)
- **Monetary Types**
  - Currency amounts (`money`, `money[]`)
- **XML Types**
  - Native XML document storage (`xml`, `xml[]`)
- **Hierarchical Types**
  - Label-tree data (`ltree`, `ltree[]`)
- **Vector Types** (requires [pgvector](https://github.com/pgvector/pgvector) extension)
  - Fixed-dimension float vector (`vector`)
  - Half-precision float vector (`halfvec`)
  - Sparse vector (`sparsevec`)
- **Enum Types**
  - User-defined PostgreSQL enum types [via `Enum` base class](docs/ENUM-TYPE.md)
- **Composite Types**
  - User-defined PostgreSQL composite (row) types [via `Composite` base class](docs/COMPOSITE-TYPE.md)
  - Access fields from [user-defined composite types](https://www.postgresql.org/docs/17/rowtypes.html) via `COMPOSITE_FIELD()` function

### PostgreSQL Operators
- **Array Operations**
  - Contains (`@>`)
  - Is contained by (`<@`)
  - Overlaps (`&&`)
  - Array aggregation with ordering
- **JSON Operations**
  - Field access (`->`, `->>`)
  - Path operations (`#>`, `#>>`)
  - JSON containment and existence operators
- **Range Operations**
  - Containment checks (in PHP value objects and for DQL queries with `@>` and `<@`)
  - Overlaps (`&&`)
- **PostGIS Spatial Operations**
  - Bounding box relationships (`<<`, `>>`, `&<`, `&>`, `|&>`, `&<|`, `<<|`, `|>>`)
  - Spatial containment (`@`, `~`)
  - Distance calculations (`<->`, `<#>`, `<<->>`, `<<#>>`, `|=|`)
  - N-dimensional operations (`&&&`)

### Functions
- **Text Search**
  - Full text search (`to_tsvector`, `to_tsquery`)
  - Pattern matching (`ilike`, `similar to`)
  - Regular expressions
  - String manipulation (`ascii`, `btrim`, `char_length`, `chr`, `decode`, `encode`, `initcap`, `lpad`, `ltrim`, `octet_length`, `quote_ident`, `quote_literal`, `quote_nullable`, `rpad`, `rtrim`, `strpos`, `translate`)
  - Trigram similarity (`similarity`, `word_similarity`, `strict_word_similarity`) (requires [pg_trgm](https://www.postgresql.org/docs/18/pgtrgm.html) extension)
  - **Hashing & Checksum** (`md5`, `sha224`, `sha256`, `sha384`, `sha512`, `crc32`, `crc32c`, `reverse` for bytea)
- **Array Functions**
  - Generic array aggregation and manipulation (`array_agg`, `array_append`, `array_prepend`, `array_remove`, `array_replace`, `array_shuffle`)
  - Array dimensions and length
  - Special aggregates (`any_value`)
- **JSON Functions**
  - JSON construction (`json_build_object`, `jsonb_build_object`)
  - JSON manipulation and transformation
  - Row to JSON (`row_to_json`, `row`)
- **Date Functions**
  - Current timestamp functions (`clock_timestamp`, `statement_timestamp`, `transaction_timestamp`)
  - Interval adjustment (`justify_days`, `justify_hours`, `justify_interval`)
- **Aggregate Functions**
  - Aggregation with ordering and distinct (`array_agg`, `json_agg`, `jsonb_agg`)
  - Statistical aggregates (`bool_and`, `bool_or`, `every`, `bit_and`, `bit_or`, `bit_xor`, `stddev`, `stddev_pop`, `var_pop`, `variance`, `corr`, `covar_pop`, `covar_samp`)
  - Special aggregates (`any_value`, `xmlagg`)
- **Hstore Functions** (requires [hstore](https://www.postgresql.org/docs/18/hstore.html) extension)
  - Key and value extraction (`akeys`, `avals`, `skeys`, `svals`)
  - Key inspection (`defined`)
  - Key deletion (`delete`)
  - JSON conversion (`hstore_to_json`, `hstore_to_json_loose`)
- **XML Functions**
  - XML aggregation, construction, manipulation, validation (`xmlagg`, `xmlcomment`, `xmlconcat`, `xml_is_well_formed`)
  - XPath querying (`xpath`, `xpath_exists`)
- **Mathematical/Arithmetic Functions**
  - Trigonometric functions (`sin`, `cos`, `tan`, `asin`, `acos`, `atan`, degree variants)
  - Hyperbolic functions (`sinh`, `cosh`, `tanh`, `asinh`, `acosh`, `atanh`)
  - Number theory functions (`gcd`, `lcm`, `factorial`, `div`)
  - Statistical functions (`erf`, `erfc`, `random_normal`)
- **Range Functions**
- **Utility Functions**
  - Type casting (`cast`)
  - Data formatting (`to_char`, `to_number`)
  - UUID generation and inspection (`uuidv4`, `uuidv7`, `uuid_extract_timestamp`, `uuid_extract_version`)
- **Vector Distance Functions**

Full documentation:
- [Available Types](docs/AVAILABLE-TYPES.md)
- [Value Objects for Range Types](docs/RANGE-TYPES.md)
- [PostgreSQL ltree Types](docs/LTREE-TYPE.md)
- [Available Functions and Operators](docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md) - Overview and cross-references
  - [Array and JSON Functions](docs/ARRAY-AND-JSON-FUNCTIONS.md)
  - [PostGIS Spatial Functions](docs/SPATIAL-FUNCTIONS-AND-OPERATORS.md)
  - [Text and Pattern Functions](docs/TEXT-AND-PATTERN-FUNCTIONS.md)
  - [Date and Range Functions](docs/DATE-AND-RANGE-FUNCTIONS.md)
  - [Mathematical Functions](docs/MATHEMATICAL-FUNCTIONS.md)
  - [Utility Functions](docs/UTILITY-FUNCTIONS.md)
  - [XML Functions](docs/XML-FUNCTIONS.md)
  - [Network Address Functions](docs/NETWORK-FUNCTIONS.md)
- [Common Use Cases and Examples](docs/USE-CASES-AND-EXAMPLES.md)
- [Spatial Types](docs/SPATIAL-TYPES.md)
- [Geometry Arrays](docs/GEOMETRY-ARRAYS.md)

## 📦 Installation

```bash
composer require martin-georgiev/postgresql-for-doctrine
```

## 🔧 Integration Guides

- [Integrating with Symfony](docs/INTEGRATING-WITH-SYMFONY.md)
- [Integrating with Laravel](docs/INTEGRATING-WITH-LARAVEL.md)
- [Integrating with Doctrine](docs/INTEGRATING-WITH-DOCTRINE.md)

## 💡 Usage Examples
See our [Common Use Cases and Examples](docs/USE-CASES-AND-EXAMPLES.md) for detailed code samples.

## 🧪 Testing

### Unit Tests
```bash
composer run-unit-tests
```

### PostgreSQL Integration Tests
We also provide integration tests that run against a real PostgreSQL database with PostGIS:

```bash
# Start PostgreSQL with PostGIS using Docker Compose
docker compose up -d

# Run integration tests
composer run-integration-tests

# Stop PostgreSQL
docker compose down -v
```

See [tests/Integration/README.md](tests/Integration/README.md) for more details.

## ⭐ Support the Project

### 💖 GitHub Sponsors
If you find this package useful for your projects, please consider [sponsoring the development via GitHub Sponsors](https://github.com/sponsors/martin-georgiev). Your support helps maintain this package, create new features, and improve documentation.

Benefits of sponsoring:
- Priority support for issues and feature requests
- Direct access to the maintainer
- Help sustain open-source development

### Other Ways to Help
- Star the repository
- [Report issues](https://github.com/martin-georgiev/postgresql-for-doctrine/issues)
- [Contribute](docs/CONTRIBUTING.md) with code or documentation
- Share the project with others

## 📝 License
This package is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## ⚡️ Powered by
<a href="https://jb.gg/OpenSource/">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://resources.jetbrains.com/storage/products/company/brand/logos/jetbrains.svg">
    <img src="https://resources.jetbrains.com/storage/products/company/brand/logos/jetbrains.svg" alt="JetBrains" height="20">
  </picture>
</a>
