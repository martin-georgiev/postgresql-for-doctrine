---
description: "Domain-specific exception patterns for DBAL types: naming, location, factory methods"
alwaysApply: true
trigger: always_on
applyTo: "**"
type: always_apply
---

# Exception Handling

## Use Domain-Specific Exceptions
NEVER use generic exceptions like `ConversionException`, `InvalidArgumentException`, or `\Throwable` directly in DBAL types. ALWAYS create and use domain-specific exceptions.

**Pattern**: `Invalid{TypeName}For{PHP|Database}Exception`

For array types: `Invalid{TypeName}ArrayItemFor{PHP|Database}Exception`

Examples:
- `InvalidUuidArrayItemForPHPException` - for PHP conversion errors in UUID arrays
- `InvalidUuidArrayItemForDatabaseException` - for database conversion errors in UUID arrays
- `InvalidPointForPHPException` - for PHP conversion errors in Point type
- `InvalidPointForDatabaseException` - for database conversion errors in Point type

**Method mapping**:
- `convertToPHPValue` / `transformArrayItemForPHP` → use `*ForPHPException`
- `convertToDatabaseValue` / `transformArrayItemForPostgres` → use `*ForDatabaseException`

**Location**: All exceptions go in `src/MartinGeorgiev/Doctrine/DBAL/Types/Exceptions/`

**Structure**: Follow existing exception patterns - extend `ConversionException`, use static factory methods like `forInvalidType()`, `forInvalidFormat()`, etc.

