# Changelog

## [2.10.3](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v2.10.2...v2.10.3) (2025-03-24)


### Bug Fixes

* Add support for Lexer v1 (allowed by ORM &lt; v2.15) ([#300](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/300)) ([16fd227](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/16fd227d1841eccfff2ffe62a4d4c0b81c9fc3e3))

## [2.10.2](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v2.10.1...v2.10.2) (2025-03-20)


### Bug Fixes

* Improve BC by deprecating `customiseFunction` instead of renaming it straight away ([#294](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/294)) ([910d328](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/910d3289fe9cb0e605765cf301ae4e86c5845e63))

## [2.10.1](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v2.10.0...v2.10.1) (2025-03-14)


### Bug Fixes

* Wrap up ORM v3 throwable when parsing fails in variadic functions ([#285](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/285)) ([59a8cb9](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/59a8cb9ed84a45a8ea7da2f19e05e921400c934b))

## [2.10.0](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v2.9.0...v2.10.0) (2025-03-13)


### Features

* Add (limited) support for `json_exists`, `json_query`, `json_scalar`, `json_serialize` and `json_value` ([#277](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/277)) ([4a26400](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/4a264003aa6ce58c65335b708dc1036d02217f08))
* Add multiple arguments support for `ARRAY` ([#279](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/279)) ([7f2b05d](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/7f2b05d21a665d44d5fac07ac0f03f1ff99647bf))


### Code Refactoring

* Introduce `BaseRegexpFunction` and `ParserException` ([#269](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/269)) ([fed0367](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/fed0367baa8cedffe309bd25e1885fb23f6449c8))
* Validate that variadic functions have only the expected count of arguments ([#274](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/274)) ([019f84d](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/019f84d23df6e85c7f5658b94c5992699e8082e3))

## [2.9.0](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v2.8.0...v2.9.0) (2025-03-12)


### Features

* Add limited support for `json_build_object` and `jsonb_build_object` ([#268](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/268)) ([2605f5a](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/2605f5accfedb23b5aa31afe5349ada77cd50258))
* Add support for `ORDER BY` clause for `array_agg()` ([#267](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/267)) ([7c64742](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/7c64742d5e3f52bb350fa630efda1ef9ac98d352))
* Add support for range functions ([#263](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/263)) ([2fa8434](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/2fa8434f517f3bf3ecb4873956bd134b4df8112b))
