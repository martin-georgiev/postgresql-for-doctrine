# Changelog

## [3.1.0](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v3.0.2...v3.1.0) (2025-05-03)


### Features

* Add support for `ARRAY_POSITION()` and `ARRAY_POSITIONS()` ([#366](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/366)) ([a1dc059](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/a1dc059965175d23a8efbf99afd2ab99a2d79564))
* Add support for `DATE_ADD()`, `DATE_SUBTRACT()` and `DATE_BIN()` ([#345](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/345)) ([c3cb08d](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/c3cb08d6af36057a0ce88fd184f91c243bcab5da))
* Add support for `JSONB_PATH_EXISTS()`, `JSONB_PATH_MATCH()`, `JSONB_PATH_QUERY()`, `JSONB_PATH_QUERY_ARRAY()` and `JSONB_PATH_QUERY_FIRST()` ([#346](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/346)) ([0cda902](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/0cda90218c4330b78c2a94f757e30e4045c70768))
* Add support for `POINT` and `POINT[]` data types ([#348](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/348)) ([18ec906](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/18ec906be3f87473b842aaa4038cc097d46e6495))
* Add support for `REGEXP_COUNT()`, `REGEXP_INSTR()` and `REGEXP_SUBSTR()` and extend support for `REGEXP_REPLACE()` ([#352](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/352)) ([9959476](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/995947610b8538e35b5a5a5233a04b22dd202bd5))
* Add support for distance operator `<@>` ([#361](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/361)) ([8dbbf8c](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/8dbbf8c71b801bd624829e04504919d730ff4a57))
* Extend existing function support with optional boolean parameters ([#347](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/347)) ([67265cc](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/67265cc84313b6cb447f6ec3a67f3b99dba4bde2))
* Extend support of `REGEXP_LIKE()`, `REGEXP_MATCH()` and `REGEXP_REPLACE()` while deprecating the legacy limited flagged variations of `FlaggedRegexpLike`, `FlaggedRegexpMatch` and `FlaggedRegexpReplace` ([#357](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/357)) ([ef688dc](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/ef688dc2d62c702fbcb85c8474e15f687de82ea5))


### Code Refactoring

* Allow multiple node mapping patterns to be used and their arguments to be validated in variadic functions ([#350](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/350)) ([e111dd2](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/e111dd28da6985324d0b9b181daf73dcbc97bb00))
* Allow node mapping in variadic functions to have different patterns, thus opening the path to a combination of node types (compared to the previous single type support) ([#349](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/349)) ([6a5ba9e](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/6a5ba9ef21b24b0e6107d85e67725a4c96f3ab8a))
* Stricter method argument types when handling variadic functions ([#343](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/343)) ([553a30c](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/553a30c2e26c4b8e69b14ad4d791dd7f7d0670d8))

## [3.0.2](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v3.0.1...v3.0.2) (2025-04-11)


### Bug Fixes

* Avoid infinite parsing loop for `GREATEST()` and `LEAST()` by using `SimpleArithmeticExpression` ([#338](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/338)) ([169192b](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/169192bb6aafc1a8851e6ab38737c27c87706bf8))

## [3.0.1](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v3.0.0...v3.0.1) (2025-04-06)


### Bug Fixes

* Restore support for unquoted string values stored in `text[]` ([#333](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/333)) ([339e988](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/339e988dd6669d6930a39deb4ad7be74ffecc78d))

## [3.0.0](https://github.com/martin-georgiev/postgresql-for-doctrine/compare/v2.10.3...v3.0.0) (2025-03-30)


### ⚠️🚨 BREAKING CHANGES

_For detailed upgrade guide read here: [UPGRADE.md](docs/UPGRADE.md)_

#### 1. Type Preservation for PostgreSQL numerical and text arrays

The library now attempts to strictly preserve the type of values when converting between PostgreSQL arrays and PHP arrays. This affects all array type handlers including `TextArray`, integer arrays, and boolean arrays.

**What changed:** Previously, numeric values could lose their type information during conversion (e.g., floats might become integers, string representations of numbers might become actual numbers). With version 3.0.0, the original data types are preserved in both directions. The change comes from PR [#304](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/304).

**Examples:**
- Integer values like `1` remain integers
- Float values like `1.5` remain floats
- String representations like `'1'` remain strings
- Boolean values like `true`/`false` remain booleans
- Scientific notation like `'1.23e5'` is preserved

#### 2. Refactored Exception Handling for JsonbArray

The exception handling for `JsonbArray` has been refactored to be more consistent with the network types approach, providing clearer error messages and better diagnostics.

**What changed:** Previously, generic exceptions were thrown when JSON array conversion failed. With version 3.0.0, specific `InvalidJsonbArrayItemForPHPException` is used with more descriptive error messages about the exact nature of the failure. The change comes from PR [#311](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/311).

### Features

#### Added new data types
* Add support for array of float types `real[]` and `double precision[]` ([#307](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/307)) ([1db35ac](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/1db35ac6f73b12e2691ca35fc6c63b0b8a3c4b28))
* Add support for network types `inet`, `inet[]`, `cidr`, `cidr[]`, `macaddr`, `macaddr[]` ([#310](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/310)) ([ba3f9f2](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/ba3f9f2833fc68f4e36ae7202396794fc43ecb63))

#### Added new functions
* Add support for `any_value()` ([#323](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/323)) ([19ee3db](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/19ee3dbd4497195bbcd3b4df7608232de0f32b8a))
* Add support for `array_shuffle()` ([#324](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/324)) ([90a9b9e](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/90a9b9e84f8ec9a0dc9fd81b2d80ae48b59f2e57))
* Add support for `xmlagg()` ([#318](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/318)) ([0b4db8a](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/0b4db8a930964b9292e7d6f79678dbc76b9d841a))

#### Extended support in some existing functions
* Add support for `NULL` value in `array_append()`, `array_replace()`, `array_prepend()`, `array_remove()` ([#322](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/322)) ([396856f](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/396856f81c40b2eefed801995c1fced455e8a8dd))
* Add support for `DISTINCT` and `ORDER BY` clauses to `json_agg()` and `jsonb_agg()` ([#317](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/317)) ([4cdc638](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/4cdc638841b23449daa9d9c0a5f9e53e15724fa3))
* Add support for `DISTINCT` clause to `array_agg()` ([#316](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/316)) ([3c46021](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/3c4602109754b345277e292e86ffd03200d91fa8))

### Code Refactoring
* Modernise the validation in active code and the associated tests when dealing with integer arrays ([#308](https://github.com/martin-georgiev/postgresql-for-doctrine/issues/308)) ([67c344e](https://github.com/martin-georgiev/postgresql-for-doctrine/commit/67c344e11e16529049422b9fe9024310594a0392))

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
