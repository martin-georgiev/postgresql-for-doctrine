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

   ℹ️ Nix allows to declare precisely an environment.
   While the learning curve is steep, it allows for reproducible installations.
   Consider using [Nix Flakes](https://nixos.wiki/wiki/flakes)
   and [Home Manager](https://home-manager.dev/).

2. Install [devenv.sh](https://devenv.sh/) (if not already installed),
   by following [Getting started @ devenv.sh](https://devenv.sh/getting-started/):

   ```bash
   nix-env --install --attr devenv -f https://github.com/NixOS/nixpkgs/tarball/nixpkgs-unstable
   ```

3. Enter the development shell from the project's root:

   ```bash
   devenv shell
   ```

   Or with [direnv](https://direnv.net/) (recommended):

   ```bash
   direnv allow
   ```

4. Launch the PostgreSQL server, for running integration tests:

   ```bash
   devenv up
   ```

The provided environment includes:

- PHP 8.1, which is the oldest PHP version supported by this project.
- Composer
- PostgreSQL 17, started by ```devenv up```.
- Pre-commit hooks for code quality.

### Local development

ℹ️ Use `devenv.local.nix` to alter the development environment.
For example, this file:

- Install [Harlequin](https://harlequin.sh/) database TUI.
- Set PHP version to 8.4.
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

The `devenv.lock` file locks the software versions installed by the devenv.
This is good for reproducibility.

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
please check that the suggested changes are in line with the project's standards.
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
  composer run-tests
  ```

## Coding practices

### How to add more array-like data types?

1. Extend `MartinGeorgiev\Doctrine\DBAL\Types\BaseArray`.

2. You must give the new data-type a unique within your application name.
   For this purpose, you can use the `TYPE_NAME` constant.
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
        $this->addNodeMapping('StringPrimary'); # corresponds to param №1 in the prototype set in setFunctionPrototype
        $this->addNodeMapping('Literal'); # corresponds to param №2 in the prototype set in setFunctionPrototype
        # Add as more node mappings if needed.
    }
}
```

⚠️ **Beware:** you cannot use **?** (e.g. the `??` operator) as part of any
function prototype in Doctrine.
It causes query parsing failures.
