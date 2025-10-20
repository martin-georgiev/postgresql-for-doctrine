# Contributing

## 🧑‍💻 Development with Devenv

This project supports [devenv.sh](https://devenv.sh/) for a consistent
development environment:

1. Install the [Nix package manager](https://nixos.org/download/#download-nix)
   (if not already installed).
   For example, install Nix via the recommended [multi-user installation](https://nixos.org/manual/nix/stable/installation/multi-user):

   ```bash
   sh <(curl --proto '=https' --tlsv1.2 -L https://nixos.org/nix/install) --daemon
   ```

   ℹ️ Nix lets you declaratively define environments.
   While the learning curve is steep, it enables reproducible installations.
   Consider using [Nix Flakes](https://nixos.wiki/wiki/flakes)
   and [Home Manager](https://home-manager.dev/).

   Configure the Nix environment:

   1. Enable `nix` command, and Flakes support:

      ```bash
      grep --quiet '^extra-experimental-features = nix-command flakes' '/etc/nix/nix.conf' ||
      sudo tee --append '/etc/nix/nix.conf' <<EOF
      # Enable nix command and flakes
      extra-experimental-features = nix-command flakes

      EOF
      ```

   2. Trust [Cachix](https://www.cachix.org/) devenv packages cache:

      ```bash
      grep --quiet '^extra-substituters = https://devenv.cachix.org' '/etc/nix/nix.conf' ||
      sudo tee --append '/etc/nix/nix.conf' <<EOF
      # Trust Cachix DevEnv
      extra-substituters = https://devenv.cachix.org
      extra-trusted-public-keys = devenv.cachix.org-1:w1cLUi8dv3hnoSPGAuibQv+f9TZLr6cv/Hm9XgU50cw=

      EOF
      ```

   3. Restart `nix-daemon` to load the new configuration:

      - for GNU/Linux systems:

        ```bash
        sudo systemctl 'restart' 'nix-daemon.service'
        ```
      - for macOS systems:

        ```bash
        sudo launchctl kickstart -k system/org.nixos.nix-daemon
        ```

2. Install [devenv.sh](https://devenv.sh/) (if not already installed),
   by following [Getting started @ devenv.sh](https://devenv.sh/getting-started/):

   - by using `nix` command if available (recommended):

     ```bash
     nix profile install nixpkgs#devenv
     ```

   - by using `nix-env` command (legacy; discouraged):

     ```bash
     nix-env --install --attr devenv -f https://github.com/NixOS/nixpkgs/tarball/nixpkgs-unstable
     ```

3. Install [direnv](https://direnv.net/) (if not already installed):

   ```bash
   nix profile install nixpkgs#direnv nixpkgs#nix-direnv
   ```

   Then hook `direnv` into your shell (once).

   - for `bash`, add this line to `~/.bashrc`:

     ```bash
     eval "$(direnv hook bash)"
     ```

   - for `zsh`, add this line to `~/.zshrc`:

     ```bash
     eval "$(direnv hook zsh)"
     ```

   - for other shells, see [Setup @ direnv documentation](https://direnv.net/docs/hook.html).

4. Enter the development shell from the project's root:

   - with `direnv` (recommended):

     ```bash
     direnv allow
     ```

   - without `direnv`:

     ```bash
     devenv shell
     ```

5. Launch the PostgreSQL server, for running integration tests:

   ```bash
   devenv up
   ```

The provided environment includes:

- PHP 8.1, which is the oldest PHP version supported by this project.
- Composer
- PostgreSQL 18 with PostGIS 3.5, started by `devenv up`.
- Pre-commit hooks (PHP-CS-Fixer, PHPStan, Rector, deptrac, ...).

### Local development

ℹ️ Use `devenv.local.nix` to alter the development environment.
It's listed in `.gitignore` and not committed.
Using local-only plaintext secrets here is acceptable.
For example, this file:

- Install [Harlequin](https://harlequin.sh/) database TUI.
- Set PHP version to 8.4.
- Change PostgreSQL related environment variables.

```nix
# devenv.local.nix
{ pkgs, lib, config, inputs, ... }:
{
  # https://devenv.sh/packages/
  packages = with pkgs; [ harlequin ];

  # https://devenv.sh/languages/
  languages.php.version = "8.4";

  # https://devenv.sh/basics/
  env = {
    POSTGRES_PASSWORD = "changeme";
    POSTGRES_PORT = 45432;
  };
}
```

### devenv.lock handling

The `devenv.lock` file pins the Nix inputs (package set and dependencies) used
by devenv.
This ensures reproducible development environments.

Update the devenv by:

1. Update dependencies:

   ```bash
   devenv update
   ```

2. Commit the changes:

   ```bash
   git add devenv.lock && git commit --message="chore: update devenv.lock"
   ```

## Before opening your first PR

For the sake of clear Git history and speedy review of your PR,
please verify that the suggested changes are in line with the project's standards.
Code style, static analysis, and file validation scripts are already provided
and can easily be run from project's root:

- Check for consistent code style:

  ```bash
  composer check-code-style
  ```

- Automatically apply fixes to the code style:

  ```bash
  composer fix-code-style
  ```

- Run static analysis for the currently configured level:

  ```bash
  composer run-static-analysis
  ```

- Run the full test suite:

  ```bash
  composer run-all-tests
  ```

## Coding practices

### How to add more array-like data types?

1. Extend `MartinGeorgiev\Doctrine\DBAL\Types\BaseArray`.

2. Give the new data type a unique name within your application.
   Use the `TYPE_NAME` constant for that purpose.
3. Depending on the new data-type nature you may have to overwrite some of
   the following methods:

    `transformPostgresArrayToPHPArray()`

    `transformArrayItemForPHP()`

    `isValidArrayItemForDatabase()`

### How to add more functions?

Most new functions will likely have a signature very similar to those already
implemented in the project.
This means new functions probably require only extending the base class
and decorating it with some behaviour.
Here are the two main steps to follow:

1. Extend `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction`.
2. Use calls to `setFunctionPrototype()` and `addNodeMapping()`
   to implement `customizeFunction()` for your new function class.

Example:

```php
<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

class ArrayAppend extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_append(%s, %s)');
        $this->addNodeMapping('StringPrimary'); // corresponds to param №1 in the prototype set in setFunctionPrototype
        $this->addNodeMapping('Literal'); // corresponds to param №2 in the prototype set in setFunctionPrototype
        // Add more node mappings if needed.
    }
}
```

⚠️ **Beware:** you cannot use **?** (e.g. the `??` operator) as part of any
function prototype in Doctrine.
It causes query parsing failures.


## Testing: Patterns and Guidelines

This project has a rich, well-structured test suite consisting of fast unit tests and database-backed integration tests. Please follow the conventions below when adding or modifying tests.

### Tools and how to run tests
- Framework: PHPUnit 10 (PHP attributes like #[Test], #[DataProvider])
- Static analysis: PHPStan (+ doctrine + phpunit extensions)
- Architecture checks: deptrac
- Code style and refactoring: PHP-CS-Fixer, Rector

Composer scripts:
- Run unit tests: `composer run-unit-tests` (uses ci/phpunit/config-unit.xml)
- Run integration tests: `composer run-integration-tests` (uses ci/phpunit/config-integration.xml)
- Run both suites: `composer run-all-tests`
- Static analysis: `composer run-static-analysis`

Integration tests require a PostgreSQL with PostGIS:
- Easiest: Docker Compose
  - Start: `docker-compose up -d`
  - Stop: `docker-compose down -v`
- Alternatively (dev shell): `devenv up`
- See tests/Integration/README.md for environment variables and details

Coverage reports are written to var/logs/test-coverage/{unit|integration}/.

### Choosing unit vs. integration tests
- Prefer unit tests for:
  - Pure value objects and small utilities (no DB/ORM)
  - Doctrine DBAL Type conversions (PHP <-> database string) using an AbstractPlatform mock
  - DQL AST function SQL generation (no DB round-trip)
- Prefer integration tests for:
  - Verifying DBAL types round-trip correctly against a real PostgreSQL
  - DQL functions/operators evaluated end-to-end against PostgreSQL
  - Scenarios relying on PostGIS or PostgreSQL-specific behavior

Keep unit tests fast and deterministic; use integration tests to validate behavior against the real database.

### Unit test patterns and conventions
- Location: tests/Unit/...
- Naming:
  - Class names end with `Test` (e.g., `PointTest`, `CidrTest`)
  - One file/class per subject
  - Concrete tests may be `final`
- Structure:
  - Extend `PHPUnit\Framework\TestCase` or an existing base test class in Unit when available
  - Use `setUp()` to create an `AbstractPlatform` mock and the subject under test when testing DBAL Types
  - Use `#[DataProvider]` for bidirectional transformation scenarios (one provider used for both PHP->DB and DB->PHP tests)
- Assertions:
  - Use domain-specific exceptions in negative tests (e.g., `InvalidCidrForPHPException`, `InvalidRangeForDatabaseException`)
  - Prefer dedicated assertion helpers provided by base classes (e.g., range equality helpers) when available
- Value Object Range tests:
  - Reuse base classes:
    - `tests/Unit/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/BaseRangeTestCase`
    - `tests/Unit/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/BaseTimestampRangeTestCase`
  - Implement the abstract factory/expectation methods and provide concise data providers
- DBAL Type unit tests:
  - Follow patterns from `PointTest`, `CidrTest`, `JsonbTest`
  - Test name retrieval (`getName()`), conversions in both directions, and invalid inputs
- DQL AST unit tests:
  - Use `tests/Unit/.../ORM/Query/AST/Functions/TestCase` to assert DQL -> SQL transformation

Anti-patterns to avoid in unit tests:
- No echo/print statements
- Avoid Reflection; test through public APIs
- Avoid raw/native SQL when verifying DBAL Types; prefer conversion tests with platform mock
- Avoid excessive PHPStan suppression; prefer PHPDoc over `@phpstan-ignore`

### Integration test patterns and organization
- Location: tests/Integration/...
- Base infrastructure:
  - Extend `tests/Integration/MartinGeorgiev/TestCase`
    - Sets up Doctrine ORM config, connection, schema `test`, caches, and ensures PostGIS
    - Provides helpers to create/drop tables, run DQL, and assert results
  - Use specialized base classes based on what you test:
    - Array types: `ArrayTypeTestCase`
    - Scalar types: `ScalarTypeTestCase`
    - Range types: `RangeTypeTestCase` (includes operator tests and `assertRangeEquals`)
    - Spatial arrays: `SpatialArrayTypeTestCase` (ARRAY[...] insertion for WKT)
- Per-type organization:
  - One integration test class per DBAL Type (e.g., `MacaddrTypeTest`, `JsonbTypeTest`, `IntegerArrayTypeTest`)
  - Implement:
    - `protected function getTypeName(): string` (Doctrine type name)
    - `protected function getPostgresTypeName(): string` (column type)
  - Data-driven tests:
    - Provide a `provideValidTransformations()` where applicable
    - Use `runDbalBindingRoundTrip($typeName, $columnType, $value)` for round-trips
- Range integration tests:
  - Extend `RangeTypeTestCase`
  - Provide a data provider for range values and add `#[DataProvider('provideValidTransformations')]` to `can_handle_range_values()`
  - Optionally add operator scenarios via `provideOperatorScenarios()` returning [name, DQL, expectedIds]

Anti-patterns to avoid in integration tests:
- Do not modify or rely on global/public schema; tests use the dedicated `test` schema created per test run
- Avoid changing existing shared fixtures/data unless strictly necessary; prefer adding new, focused fixtures
- No echo/print statements

### Base test classes and shared utilities
Commonly used bases and what they provide:
- Unit (Value Objects):
  - `BaseRangeTestCase`: creation/formatting/boundary tests with abstract factory methods
  - `BaseTimestampRangeTestCase`: extends the above with timestamp-specific boundary and helpers
- Unit (DBAL Types):
  - `tests/Unit/.../DBAL/Types/BaseRangeTestCase`: negative cases and conversions for range DBAL types
- Unit (ORM functions):
  - `tests/Unit/.../ORM/Query/AST/Functions/TestCase`: DQL to SQL transformation checks
- Integration (DBAL Types):
  - `TestCase`: connection/schema setup, round-trip helper, assertions
  - `ArrayTypeTestCase`, `ScalarTypeTestCase`, `RangeTypeTestCase`, `SpatialArrayTypeTestCase`

Prefer extending these base classes over duplicating setup/utility code.

### Exception handling and error testing
- Use domain-specific exceptions consistently:
  - `convertToPHPValue()` should throw `...ForPHPException`
  - `convertToDatabaseValue()` should throw `...ForDatabaseException`
- In tests, assert the exact exception class and include `expectExceptionMessage()` when the message is part of the contract
- For range equality in integration, use the provided `assertRangeEquals()` which compares string representation and emptiness

### Test data and fixtures
- Entities for integration live under `fixtures/MartinGeorgiev/Doctrine/Entity` and are registered via attributes
- If a new fixture is necessary, add it under the same namespace and keep it minimal and reusable
- Range/operator tests seed their own tables (see `RangeTypeTestCase`’s `createRangeOperatorsTable()` and insert helpers)

### Code style in test files
- Use PHP attributes `#[Test]` and `#[DataProvider]` (PHPUnit 10)
- Descriptive dataset names in data providers improve failure readability
- Prefer clear method names starting with verbs: `can_...`, `throws_...`, `dql_is_...`
- Keep tests small and focused; avoid commentary that restates code; write comments only to explain intent or PostgreSQL-specific behavior
- Maintain alphabetical order in documentation blocks and lists when applicable

### Minimal examples
Unit test for a DBAL Type (mock platform, bidirectional conversions):

```php
final class InetTest extends TestCase
{
    /**
     * @var AbstractPlatform&MockObject
     */
    private MockObject $platform;
    private Inet $fixture;

    protected function setUp(): void
    {
        $this->platform = $this->createMock(AbstractPlatform::class);
        $this->fixture = new Inet();
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_from_php_value(?string $php, ?string $pg): void
    {
        $this->assertEquals($pg, $this->fixture->convertToDatabaseValue($php, $this->platform));
    }

    #[DataProvider('provideValidTransformations')]
    #[Test]
    public function can_transform_to_php_value(?string $php, ?string $pg): void
    {
        $this->assertEquals($php, $this->fixture->convertToPHPValue($pg, $this->platform));
    }
}
```

Integration test for an array type:

```php
final class TextArrayTypeTest extends ArrayTypeTestCase
{
    protected function getTypeName(): string { return 'text[]'; }
    protected function getPostgresTypeName(): string { return 'TEXT[]'; }
    #[DataProvider('provideValidTransformations')] #[Test]
    public function can_handle_array_values(string $name, array $value): void { parent::can_handle_array_values($name, $value); }
}
```

Range integration test:

```php
final class Int4RangeTypeTest extends RangeTypeTestCase
{
    protected function getTypeName(): string { return 'int4range'; }
    protected function getPostgresTypeName(): string { return 'INT4RANGE'; }
    #[DataProvider('provideValidTransformations')] #[Test]
    public function can_handle_range_values(string $name, RangeValueObject $range): void { parent::can_handle_range_values($name, $range); }
}
```

If unsure which base to extend or how to structure a new test, mirror a nearby, similar test and keep changes minimal and consistent with the patterns above.