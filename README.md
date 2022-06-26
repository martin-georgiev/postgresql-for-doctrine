[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/martin-georgiev/postgresql-for-doctrine/badges/quality-score.png)](https://scrutinizer-ci.com/g/martin-georgiev/postgresql-for-doctrine/?branch=master)
[![Build Status](https://github.com/martin-georgiev/postgresql-for-doctrine/actions/workflows/ci/badge.svg)](https://github.com/martin-georgiev/postgresql-for-doctrine/actions/workflows/ci.yml)
[![Coverage Status](https://coveralls.io/repos/github/martin-georgiev/postgresql-for-doctrine/badge.svg?branch=master)](https://coveralls.io/github/martin-georgiev/postgresql-for-doctrine?branch=master)
[![Latest Stable Version](https://poser.pugx.org/martin-georgiev/postgresql-for-doctrine/version)](https://packagist.org/packages/martin-georgiev/postgresql-for-doctrine)
[![Total Downloads](https://poser.pugx.org/martin-georgiev/postgresql-for-doctrine/downloads)](https://packagist.org/packages/martin-georgiev/postgresql-for-doctrine)
----
## What's this?
This package provides Doctrine support for some specific PostgreSQL 9.4+ features:

* Support of JSONB and some array data-types (at present integers, BOOL, TEXT and JSONB)
* Implementation of the most commonly used functions and operators when working with array and JSON data-types
* Functions for text search
* Aggregate functions

It can be integrated in a simple manner with Symfony, Laravel and other frameworks that make use of Doctrine.

You can easily extend package's behaviour with your own array-like data-types or other desired functions. Read more about this in the [contributing guide](docs/CONTRIBUTING.md).

----
## What is available?
Full set of the available types can be found [here](docs/AVAILABLE-TYPES.md).

Full set of the available functions and extra operators can be found [here](docs/AVAILABLE-FUNCTIONS-AND-OPERATORS.md).

----
## How to install?
Easiest and recommended way is with [Composer](https://getcomposer.org/download/)

    composer require martin-georgiev/postgresql-for-doctrine

----
## How to integrate with your framework?
Read the guide with examples for [Symfony](docs/INTEGRATING-WITH-SYMFONY.md).

Read the guide with examples for [Laravel](docs/INTEGRATING-WITH-LARAVEL.md).

Read the guide with examples for [Doctrine](docs/INTEGRATING-WITH-DOCTRINE.md).

----

Check for [common use-cases, examples and known errors](docs/USE-CASES-AND-EXAMPLES.md).

----
## License
This package is licensed under the MIT License.
