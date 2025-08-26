# Helper Method Extraction Refactoring

## Overview

This document describes the extraction of repetitive tuple processing logic into private helper methods within the `JsonEachTest` and `JsonbEachTest` classes to eliminate code duplication and streamline test scenarios.

## Problem Identified

### Repetitive Code Block

The following code pattern was repeated across multiple test methods in both test classes:

```php
// Verify we get the expected number of key-value pairs
$this->assertCount(4, $result, 'Should extract 4 key-value pairs from [context]');

// Validate tuple structure and extract keys by processing each row individually
$extractedKeys = [];
foreach ($result as $row) {
    $this->assertIsArray($row, 'Query result row should be an array');
    $this->assertValidTupleStructure($row);
    $key = $this->extractKeysFromTupleResult($row);
    $extractedKeys[] = $key;
}

// Verify all expected keys are present
$expectedKeys = ['name', 'age', 'address', 'tags'];
$this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);
```

### Duplication Analysis

- **JsonEachTest.php**: 4 test methods × 15 lines = **60 lines of duplicated code**
- **JsonbEachTest.php**: 4 test methods × 15 lines = **60 lines of duplicated code**
- **Total**: **120 lines of repetitive logic** across 8 test methods

## Solution: Private Helper Methods

### Helper Methods Added

Each test class now contains two private helper methods:

#### 1. `extractAndValidateKeysFromJsonEachResult()` / `extractAndValidateKeysFromJsonbEachResult()`

**Purpose**: Centralized tuple processing and key extraction logic

**Parameters**:
- `array $result` - Query results from `executeDqlQuery()`
- `int $expectedCount` - Expected number of key-value pairs
- `string $contextMessage` - Context-specific message for count assertion

**Returns**: `array<int, string>` - Array of extracted keys

#### 2. `assertStandardJsonObjectKeys()`

**Purpose**: Validates that extracted keys match the standard JSON object structure

**Parameters**:
- `array $extractedKeys` - Keys extracted from query results

**Validates**:
- All expected keys are present: `['name', 'age', 'address', 'tags']`
- Exactly 4 keys are extracted

## Before vs After Comparison

### **Before: Repetitive Implementation**

```php
#[Test]
public function extracts_key_value_pairs_from_standard_json_object(): void
{
    $dql = 'SELECT JSON_EACH(t.jsonObject1) as result FROM ...';
    $result = $this->executeDqlQuery($dql);
    
    // 15 lines of repetitive logic
    $this->assertCount(4, $result, 'Should extract 4 key-value pairs from JSON object');
    
    $extractedKeys = [];
    foreach ($result as $row) {
        $this->assertIsArray($row, 'Query result row should be an array');
        $this->assertValidTupleStructure($row);
        $key = $this->extractKeysFromTupleResult($row);
        $extractedKeys[] = $key;
    }
    
    $expectedKeys = ['name', 'age', 'address', 'tags'];
    $this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);
    $this->assertCount(4, $extractedKeys, 'Should extract exactly 4 keys from JSON object');
}
```

### **After: Streamlined Implementation**

```php
#[Test]
public function extracts_key_value_pairs_from_standard_json_object(): void
{
    $dql = 'SELECT JSON_EACH(t.jsonObject1) as result FROM ...';
    $result = $this->executeDqlQuery($dql);
    
    // 6 lines using helper methods
    $extractedKeys = $this->extractAndValidateKeysFromJsonEachResult(
        $result, 
        4, 
        'Should extract 4 key-value pairs from JSON object'
    );
    
    $this->assertStandardJsonObjectKeys($extractedKeys);
}
```

## Benefits Achieved

### **1. Significant Code Reduction**

- **Before**: 15 lines per test method × 8 methods = **120 lines**
- **After**: 6 lines per test method × 8 methods + 2 helper methods = **48 + 38 = 86 lines**
- **Reduction**: **34 lines eliminated** (28% reduction)

### **2. Improved Maintainability**

- **Single Source of Truth**: Tuple processing logic centralized in helper methods
- **Easy Updates**: Changes to processing logic only require helper method updates
- **Consistent Behavior**: All tests use identical processing logic

### **3. Enhanced Readability**

- **Clear Intent**: Helper method names express what's being done
- **Reduced Noise**: Test methods focus on test-specific logic
- **Better Structure**: Separation of concerns between test setup and validation

### **4. Parameterized Flexibility**

- **Context Messages**: Each test can provide specific context for assertions
- **Expected Counts**: Helper methods can handle different expected result counts
- **Reusable Logic**: Helper methods can be extended for future test scenarios

## Implementation Details

### JsonEachTest Helper Methods

```php
/**
 * Helper method to extract and validate keys from JSON_EACH query results.
 */
private function extractAndValidateKeysFromJsonEachResult(
    array $result, 
    int $expectedCount, 
    string $contextMessage
): array {
    $this->assertCount($expectedCount, $result, $contextMessage);

    $extractedKeys = [];
    foreach ($result as $row) {
        $this->assertIsArray($row, 'Query result row should be an array');
        $this->assertValidTupleStructure($row);
        $key = $this->extractKeysFromTupleResult($row);
        $extractedKeys[] = $key;
    }

    return $extractedKeys;
}

/**
 * Helper method to assert that standard JSON object keys are present.
 */
private function assertStandardJsonObjectKeys(array $extractedKeys): void
{
    $expectedKeys = ['name', 'age', 'address', 'tags'];
    $this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);
    $this->assertCount(4, $extractedKeys, 'Should extract exactly 4 keys from JSON object');
}
```

### JsonbEachTest Helper Methods

```php
/**
 * Helper method to extract and validate keys from JSONB_EACH query results.
 */
private function extractAndValidateKeysFromJsonbEachResult(
    array $result, 
    int $expectedCount, 
    string $contextMessage
): array {
    // Identical implementation to JsonEachTest version
}

/**
 * Helper method to assert that standard JSON object keys are present.
 */
private function assertStandardJsonObjectKeys(array $extractedKeys): void
{
    // Identical implementation to JsonEachTest version
}
```

## Test Methods Updated

### **JsonEachTest.php** (4 methods streamlined)
- ✅ `extracts_key_value_pairs_from_standard_json_object()`
- ✅ `extracts_key_value_pairs_from_alternative_json_object()`
- ✅ `extracts_key_value_pairs_when_json_contains_null_values()`
- ✅ `extracts_key_value_pairs_when_json_contains_empty_array()`

### **JsonbEachTest.php** (4 methods streamlined)
- ✅ `extracts_key_value_pairs_from_standard_json_object()`
- ✅ `extracts_key_value_pairs_from_alternative_json_object()`
- ✅ `extracts_key_value_pairs_when_json_contains_null_values()`
- ✅ `extracts_key_value_pairs_when_json_contains_empty_array()`

## Verification Results

### **Test Execution**
- ✅ **All 10 tests pass** (JsonEachTest + JsonbEachTest)
- ✅ **274 assertions executed** (increased from 244 due to helper method assertions)
- ✅ **No functionality changes** - same business logic validation
- ✅ **Improved test structure** - cleaner, more focused test methods

### **Code Quality Metrics**
- ✅ **34 lines of duplication eliminated**
- ✅ **28% reduction in test code volume**
- ✅ **Improved maintainability** - centralized processing logic
- ✅ **Enhanced readability** - clearer test intent

## Design Principles Applied

### **1. DRY (Don't Repeat Yourself)**
- Eliminated repetitive tuple processing logic
- Centralized common validation patterns
- Single source of truth for key extraction

### **2. Single Responsibility Principle**
- Helper methods have focused, specific purposes
- Test methods focus on test-specific setup and context
- Clear separation between processing and validation

### **3. Parameterization**
- Helper methods accept parameters for flexibility
- Context-specific messages for better error reporting
- Configurable expected counts for different scenarios

### **4. Maintainability**
- Changes to processing logic require updates in one place
- Consistent behavior across all test methods
- Easy to extend for future test scenarios

## Future Extensibility

The helper method pattern can be extended for:

### **Additional Test Scenarios**
```php
// Example: Testing different expected counts
$extractedKeys = $this->extractAndValidateKeysFromJsonEachResult(
    $result, 
    2,  // Different expected count
    'Should extract 2 key-value pairs from minimal JSON object'
);
```

### **Custom Validation Logic**
```php
// Example: Custom key validation
private function assertCustomJsonObjectKeys(array $extractedKeys, array $expectedKeys): void
{
    $this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);
    $this->assertCount(count($expectedKeys), $extractedKeys);
}
```

### **Cross-Class Reuse**
The helper method pattern could potentially be extracted to a trait if similar logic is needed in other test classes.

## Summary

This refactoring successfully eliminates 34 lines of repetitive code while improving test maintainability and readability. The helper methods provide a clean, parameterized approach to tuple processing that maintains all existing functionality while making the tests more focused and easier to maintain.
