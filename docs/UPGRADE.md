# Upgrade Instructions

## How to Upgrade to Version 3.0

### 1. Review type handling in your code
If your application relies on automatic type conversion between PostgreSQL and PHP (e.g., expecting string numbers to be converted to actual numbers or vice versa), you'll need to update your code to explicitly handle type conversion where needed.

```php
// Before: Might convert '1.0' to integer 1
$tags = $entity- >getTags(); // ['1.0', '2.5']
$numericValue = $tags[0] + 2; // Would work even if string

// After: Preserves '1.0' as string
$tags = $entity->getTags(); // ['1.0', '2.5']
$numericValue = (float)$tags[0] + 2; // Explicit conversion needed
```

### 2. Update your code to handle exceptions
If you're catching specific exception types when working with `JsonbArray`, update your exception handling to catch the new `InvalidJsonItemForPHPException` and `InvalidJsonArrayItemForPHPException`.

```php
// Before
try {
    $jsonArray = $jsonbArrayType->convertToPHPValue($postgresValue, $platform);
} catch (\MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\TypeException $e) {
    // Handle exception
}

// After
try {
    $jsonArray = $jsonbArrayType->convertToPHPValue($postgresValue, $platform);
} catch (\MartinGeorgiev\Doctrine\DBAL\Types\Exceptions\InvalidJsonArrayItemForPHPException $e) {
    // Handle exception
}
```

### 3. Test thoroughly
Since these changes affect data type handling at a fundamental level, thoroughly test all database interactions, especially those involving array types, to ensure your application handles the preserved types correctly.

