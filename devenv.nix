{
  pkgs,
  lib,
  config,
  inputs,
  ...
}:
let
  inherit (config.languages.php.packages) composer;
  composerCommand = lib.meta.getExe composer;
  phpCommand = lib.meta.getExe config.languages.php.package;
  pythonPackages = pkgs.python3Packages;
in
{
  name = "PostgreSQL for Doctrine";

  # https://devenv.sh/basics/
  # Set these locally in devenv.local.nix
  env = {
    POSTGRES_USER = lib.mkDefault "postgres";
    POSTGRES_PASSWORD = lib.mkDefault "postgres";
    POSTGRES_DB = lib.mkDefault "postgres_doctrine_test";
    POSTGRES_PORT = lib.mkDefault 5432;
  };

  # https://devenv.sh/packages/
  packages = with pkgs; [ git ];

  # https://devenv.sh/languages/
  languages.php = {
    enable = true;
    # PHP 8.1 is this project least supported PHP version.
    version = "8.1";
    extensions = [
      "ctype"
      "dom"
      "filter"
      "iconv"
      "mbstring"
      "openssl"
      "pdo"
      "pdo_pgsql"
      "pdo_sqlite"
      "tokenizer"
      "xdebug"
      "xmlwriter"
    ];

    ini = lib.concatStringsSep "\n" [
        "xdebug.mode=develop"
        "memory_limit=256m"
        "error_reporting=E_ALL"
      ];
  };

  # https://devenv.sh/processes/
  # processes.cargo-watch.exec = "cargo-watch";

  # https://devenv.sh/services/
  services.postgres = {
    enable = true;

    # Use PostgreSQL 18 to match Docker Compose and CI
    package = pkgs.postgresql_18;

    listen_addresses = "127.0.0.1";
    port = config.env.POSTGRES_PORT;

    initialDatabases = [ { name = config.env.POSTGRES_DB; } ];

    # Enable PostGIS extension
    extensions = extensions: [
      extensions.postgis
    ];

    initialScript = ''
      -- Create role if it doesn't exist, or update password if it does
      DO $$
      BEGIN
        IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = '${config.env.POSTGRES_USER}') THEN
          CREATE ROLE "${config.env.POSTGRES_USER}" WITH SUPERUSER LOGIN PASSWORD '${config.env.POSTGRES_PASSWORD}';
        ELSE
          ALTER ROLE "${config.env.POSTGRES_USER}" WITH SUPERUSER LOGIN PASSWORD '${config.env.POSTGRES_PASSWORD}';
        END IF;
      END
      $$;

      -- Set database owner
      ALTER DATABASE "${config.env.POSTGRES_DB}" OWNER TO "${config.env.POSTGRES_USER}";

      -- Enable PostGIS extension in the database
      \c ${config.env.POSTGRES_DB}
      CREATE EXTENSION IF NOT EXISTS postgis;
    '';
  };

  # https://devenv.sh/scripts/
  scripts.php-modules.exec = ''
    set -o 'errexit' -o 'pipefail'

    echo 'Installed PHP modules'
    echo '---------------------'

    ${phpCommand} -m |
    command grep --invert-match --extended-regexp '^(|\[.*\])$' |
    command tr '\n' ' ' |
    command fold --spaces

    printf '\n---------------------\n'
  '';

  enterShell = ''
    echo "🚀 ${config.name} Development Environment"
    git --version
    composer  --version
    php-modules
    postgres --version
    echo "Storage: ''${PGDATA}"
    export PATH="${config.env.DEVENV_ROOT}/bin:$PATH"
  '';

  # https://devenv.sh/tasks/
  tasks = {
    "devenv:enterShell:install:composer" = {
      description = "Install composer packages";
      before = [ "devenv:enterShell" ];
      exec = ''
        set -o 'errexit'
        [[ -e "''${DEVENV_ROOT}/composer.json" ]] &&
        ${composerCommand} 'install'
      '';
    };
    "devenv:services:reset:postgresql" = {
      description = "Reset PostgreSQL data";
      exec = ''
        set -o 'errexit'
        echo "Deleting PostgreSQL data in ''${PGDATA}"
        [[ -e "''${PGDATA}" ]] &&
        rm -r "''${PGDATA}"
      '';
    };
  };

  # https://devenv.sh/tests/
  enterTest = ''
    echo "Running tests"
    git --version | grep --color=auto "${pkgs.git.version}"
    php --version | grep --color=auto "${config.languages.php.package.version}"
    composer check-code-style
    composer run-static-analysis
    composer run-all-tests
  '';

  # https://devenv.sh/git-hooks/
  git-hooks.hooks = {
    # Conventional Commits
    commitizen.enable = true;

    # Markdown files
    # markdownlint.enable = true;
    # mdformat = rec {
    #   enable = true;
    #   package = pythonPackages.mdformat;
    #   extraPackages = with pythonPackages; [
    #     mdformat-beautysh
    #     mdformat-gfm
    #     mdformat-tables
    #   ];
    # };

    composer-validate = rec {
      enable = true;
      name = "composer validate";
      package = composer;
      files = "composer.json$";
      pass_filenames = false;
      entry = ''"${lib.meta.getExe package}" validate'';
      stages = [
        "pre-commit"
        "pre-push"
      ];
    };

    composer-audit = rec {
      enable = true;
      name = "composer audit";
      after = [ "composer-validate" ];
      package = composer;
      files = "composer.json$";
      verbose = true;
      pass_filenames = false;
      entry = ''"${lib.meta.getExe package}" audit'';
      stages = [
        "pre-commit"
        "pre-push"
      ];
    };

    rector = rec {
      enable = true;
      name = "Rector";
      after = [ "composer-validate" ];
      inherit (config.languages.php) package;
      files = "\\.php$";
      pass_filenames = true;
      entry = "${lib.meta.getExe package} '${config.env.DEVENV_ROOT}/bin/rector' 'process'";
      args = [
        "--dry-run"
        "--config=${config.env.DEVENV_ROOT}/ci/rector/config.php"
      ];
    };

    php-cs-fixer = rec {
      enable = true;
      name = "PHP Coding Standards Fixer";
      after = [
        "composer-validate"
        "rector"
      ];
      inherit (config.languages.php) package;
      files = "\\.php$";
      pass_filenames = true;
      entry = "${lib.meta.getExe package} '${config.env.DEVENV_ROOT}/bin/php-cs-fixer' 'fix'";
      args = [
        "--dry-run"
        "--config=${config.env.DEVENV_ROOT}/ci/php-cs-fixer/config.php"
        "--show-progress=none"
        "--no-interaction"
        "--diff"
      ];
    };

    phpstan = rec {
      enable = true;
      name = "PHPStan";
      after = [ "composer-validate" ];
      inherit (config.languages.php) package;
      pass_filenames = false;
      entry = "${lib.meta.getExe package} '${config.env.DEVENV_ROOT}/bin/phpstan' 'analyse'";
      args = [ "--configuration=${config.env.DEVENV_ROOT}/ci/phpstan/config.neon" ];
    };

    deptrac = rec {
      enable = true;
      name = "Deptrac";
      after = [ "composer-validate" ];
      inherit (config.languages.php) package;
      pass_filenames = false;
      entry = "${lib.meta.getExe package} '${config.env.DEVENV_ROOT}/bin/deptrac' 'analyze'";
      args = [
        "--config-file=./ci/deptrac/config.yml"
        "--cache-file=./ci/deptrac/.cache"
        "--no-interaction"
        "--no-progress"
      ];
    };

  };

  # See full reference at https://devenv.sh/reference/options/
}
