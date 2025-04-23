[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/martin-georgiev/postgresql-for-doctrine/badges/quality-score.png)](https://scrutinizer-ci.com/g/martin-georgiev/postgresql-for-doctrine/?branch=main)
[![Coverage Status](https://coveralls.io/repos/github/martin-georgiev/postgresql-for-doctrine/badge.svg?branch=main)](https://coveralls.io/github/martin-georgiev/postgresql-for-doctrine?branch=main)
[![Latest Stable Version](https://poser.pugx.org/martin-georgiev/postgresql-for-doctrine/version)](https://packagist.org/packages/martin-georgiev/postgresql-for-doctrine)
[![Total Downloads](https://poser.pugx.org/martin-georgiev/postgresql-for-doctrine/downloads)](https://packagist.org/packages/martin-georgiev/postgresql-for-doctrine)

# PostgreSQL for Doctrine

Enhances Doctrine with PostgreSQL-specific features and functions. Supports PostgreSQL 9.4+ and PHP 8.1+.

## Quick Start

```php
// Register types with Doctrine
Type::addType('jsonb', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\Jsonb");
Type::addType('text[]', "MartinGeorgiev\\Doctrine\\DBAL\\Types\\TextArray");

// Use in your Doctrine entities
#[ORM\Column(type: 'jsonb')]
private array $data;

#[ORM\Column(type: 'text[]')]
private array $tags;

// Use in DQL
$query = $em->createQuery('
    SELECT e
    FROM App\Entity\Post e
    WHERE CONTAINS(e.tags, ARRAY[:tags]) = TRUE
    AND JSON_GET_FIELD(e.data, :field) = :value
');
```

## 🚀 Features Highlight

This package provides comprehensive Doctrine support for PostgreSQL features:

### Data Types
- **Array Types**
  - Integer arrays (`int[]`, `smallint[]`, `bigint[]`)
  - Float arrays (`real[]`, `double precision[]`)
  - Text arrays (`text[]`)
  - Boolean arrays (`bool[]`)
  - JSONB arrays (`jsonb[]`)
- **JSON Types**
  - Native JSONB support
  - JSON field operations
  - JSON construction and manipulation
- **Network Types**
  - IP addresses (`inet`, `inet[]`)
  - Network CIDR notation (`cidr`, `cidr[]`)
  - MAC addresses (`macaddr`, `macaddr[]`)

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

### Functions
- **Text Search**
  - Full text search (`to_tsvector`, `to_tsquery`)
  - Pattern matching (`ILIKE`, `SIMILAR TO`)
  - Regular expressions
- **Array Functions**
  - Array aggregation (`array_agg`)
  - Array manipulation (`array_append`, `array_prepend`, `array_remove`, `array_replace`, `array_shuffle`)
  - Array dimensions and length
- **JSON Functions**
  - JSON construction (`json_build_object`, `jsonb_build_object`)
  - JSON manipulation and transformation
- **Date Functions**
- **Aggregate Functions**
  - Aggregation with ordering and distinct (`array_agg`, `json_agg`, `jsonb_agg`)
  - Special aggregates (`any_value`, `xmlagg`)

Full documentation:
- [Available Types](docs/AVAILABLE-TYPES.md)
- [Available Functions and Operators](docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md)
- [Common Use Cases and Examples](docs/USE-CASES-AND-EXAMPLES.md)

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
We also provide integration tests that run against a real PostgreSQL database to ensure compatibility:

```bash
# Start PostgreSQL using Docker Compose
docker-compose up -d

# Run integration tests
composer run-integration-tests

# Stop PostgreSQL
docker-compose down -v
```

See [tests-integration/README.md](tests-integration/README.md) for more details.

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
