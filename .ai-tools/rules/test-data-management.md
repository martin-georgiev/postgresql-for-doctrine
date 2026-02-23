---
description: "Integration test fixtures: use central data, design resilient to fixture growth"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Test Data Management

## Use Central Fixture Data
When writing integration tests, prefer using existing fixture data from the central test fixtures rather than creating test-specific data insertions. Test-specific `INSERT` statements in test methods create maintenance burden and duplicate data definitions.

**Do**: Reference existing fixture IDs that have the required characteristics
**Don't**: Create `private function insertSpecialData()` (or similarly named) methods in individual test classes

## Design Tests Resilient to Fixture Growth
Tests that count total rows or rely on exact result set sizes are brittle. When new fixture data is added, these tests break even though the functionality being tested is correct.

**Do**: Filter queries to specific IDs, or accept that counts may grow
**Don't**: Assert exact counts like `assertCount(10, $result)` when the count depends on total fixture size

**Before adding new fixtures**:
1. Search for tests that don't filter by specific IDs
2. Check if any spatial functions have limitations with the new geometry type
3. Add explicit ID filters to known compatible fixtures (e.g., `WHERE id = 4` or `WHERE id IN (1, 2, 3)`)

**Example**: PostGIS 3.4 doesn't support `ST_DFullyWithin` with arc geometries (CircularString/CompoundCurve). Tests scanning all rows will fail when CompoundCurve fixtures are added.

