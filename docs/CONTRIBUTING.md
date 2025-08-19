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

- PHP 8.1, which is the oldest PHP version supported by this project.
- Composer
- PostgreSQL 17 with PostGIS 3.4, started by `devenv up`.
- Pre-commit hooks (PHP-CS-Fixer, PHPStan, Rector, deptrac, ...).

### Local development

ℹ️ Use `devenv.local.nix` to alter the development environment.
It's listed in `.gitignore` and not committed.
Using local-only plaintext secrets here is acceptable.
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
  composer run-tests
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
