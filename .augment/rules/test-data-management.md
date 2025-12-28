# Test Data Management

## Use Central Fixture Data
When writing integration tests, prefer using existing fixture data from the central test fixtures rather than creating test-specific data insertions. Test-specific `INSERT` statements in test methods create maintenance burden and duplicate data definitions.

**Do**: Reference existing fixture IDs that have the required characteristics
**Don't**: Create `private function insertSpecialData()` methods in individual test classes

## Design Tests Resilient to Fixture Growth
Tests that count total rows or rely on exact result set sizes are brittle. When new fixture data is added, these tests break even though the functionality being tested is correct.

**Do**: Filter queries to specific IDs, or accept that counts may grow
**Don't**: Assert exact counts like `assertCount(10, $result)` when the count depends on total fixture size

