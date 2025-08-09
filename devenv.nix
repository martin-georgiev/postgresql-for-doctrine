{
  pkgs,
  lib,
  config,
  inputs,
  ...
}:

{
  name = "PostgreSQL for Doctrine";

  # https://devenv.sh/basics/
  env = {
    POSTGRES_USER = "postgres";
    POSTGRES_PASSWORD = "postgres";
    POSTGRES_DB = "postgres_doctrine_test";
    POSTGRES_PORT = 5432;
  };

  # https://devenv.sh/packages/
  packages = with pkgs; [ git ];

  # https://devenv.sh/languages/
  languages.php = {
    enable = true;
    version = "8.1";
    extensions = [
      "dom"
      "filter"
      "iconv"
      "mbstring"
      "openssl"
      "pdo_pgsql"
      "pdo_sqlite"
      "xdebug"
      "xmlwriter"
    ];
    disableExtensions = [
      "bcmath"
      "calendar"
      "ctype"
      "curl"
      "date"
      "exif"
      "fileinfo"
      "ftp"
      "gd"
      "gettext"
      "gmp"
      "imap"
      "intl"
      "ldap"
      "mysqli"
      "mysqlnd"
      "opcache"
      "pcntl"
      "pdo_mysql"
      "pdo_odbc"
      "posix"
      "readline"
      "session"
      "simplexml"
      "soap"
      "sockets"
      "sodium"
      "sysvsem"
      "xmlreader"
      "zip"
      "zlib"
    ];

    ini = lib.concatStringsSep "\n" [
      "xdebug.mode = develop"
      "memory_limit = 256m"
    ];
  };

  # https://devenv.sh/processes/
  # processes.cargo-watch.exec = "cargo-watch";

  # https://devenv.sh/services/
  services.postgres = {
    enable = true;

    listen_addresses = "127.0.0.1";
    port = config.env.POSTGRES_PORT;

    initialDatabases = [ { name = config.env.POSTGRES_DB; } ];

    initialScript = ''
      CREATE ROLE "${config.env.POSTGRES_USER}"
        WITH SUPERUSER LOGIN PASSWORD '${config.env.POSTGRES_PASSWORD}';
    '';
  };

  # https://devenv.sh/scripts/
  scripts.php-modules.exec = ''
    set -o 'errexit' -o 'pipefail'

    echo 'Installed PHP modules'
    echo '---------------------'

    php -m |
    command grep --invert-match --extended-regexp '^(|\[.*\])$' |
    command tr '\n' ' ' |
    command fold --spaces

    printf '\n---------------------\n'
  '';

  enterShell = ''
    git --version
    composer  --version
    php-modules
    postgres --version
    echo "Storage: ''${PGDATA}"
    export PATH="${config.env.DEVENV_ROOT}/bin:$PATH"
  '';

  # https://devenv.sh/tasks/
  # tasks = {
  #   "myproj:setup".exec = "mytool build";
  #   "devenv:enterShell".after = [ "myproj:setup" ];
  # };

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
    phpstan = rec {
      enable = true;
      name = "PHPStan";
      inherit (config.languages.php) package;
      pass_filenames = false;
      entry = "${package}/bin/php '${config.env.DEVENV_ROOT}/bin/phpstan' 'analyse'";
      args = [ "--configuration=${config.env.DEVENV_ROOT}/ci/phpstan/config.neon" ];
    };

    php-cs-fixer = rec {
      enable = true;
      name = "PHP Coding Standards Fixer";
      inherit (config.languages.php) package;
      files = ".*\.php$";
      entry = "${package}/bin/php '${config.env.DEVENV_ROOT}/bin/php-cs-fixer' 'fix'";
      args = [
        "--dry-run"
        "--config=${config.env.DEVENV_ROOT}/ci/php-cs-fixer/config.php"
        "--show-progress=none"
        "--no-interaction"
      ];
    };

    rector = rec {
      enable = true;
      name = "Rector";
      inherit (config.languages.php) package;
      files = ".*\.php$";
      pass_filenames = false;
      entry = "${package}/bin/php '${config.env.DEVENV_ROOT}/bin/rector' 'process'";
      args = [
        "--dry-run"
        "--config=${config.env.DEVENV_ROOT}/ci/rector/config.php"
      ];
    };
  };

  # See full reference at https://devenv.sh/reference/options/
}
