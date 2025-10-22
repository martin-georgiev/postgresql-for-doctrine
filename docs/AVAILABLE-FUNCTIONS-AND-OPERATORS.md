# Available Functions and Operators

This document provides an overview of PostgreSQL functions and operators available in this library. For detailed documentation of specific function categories, see the specialized documentation files linked below.

## Operator Conflicts and Usage Notes

**⚠️ Important**: Some PostgreSQL operators have multiple meanings depending on the data types involved. This library provides specific DQL function names to avoid conflicts:

| Operator | Array/JSON Usage | Spatial Usage | Text/Pattern Usage |
|---|---|---|---|
| `@>` | `CONTAINS` (arrays contain elements) | Works automatically with geometry/geography | N/A |
| `<@` | `IS_CONTAINED_BY` (element in array) | Works automatically with geometry/geography | N/A |
| `@` | N/A | `SPATIAL_CONTAINED_BY` (bounding box contained) | N/A |
| `~` | N/A | `SPATIAL_CONTAINS` (bounding box contains) | `REGEXP` (text pattern matching) |
| `&&` | `OVERLAPS` (arrays/ranges overlap) | Works automatically with geometry/geography | N/A |

**Usage Guidelines:**
- **Arrays/JSON**: Use `CONTAINS`, `IS_CONTAINED_BY`, `OVERLAPS` for array and JSON operations → [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md)
- **Spatial**: Use `SPATIAL_CONTAINS`, `SPATIAL_CONTAINED_BY` for explicit spatial bounding box operations → [PostGIS Spatial Functions](SPATIAL-FUNCTIONS-AND-OPERATORS.md)
- **Text**: Use `REGEXP`, `IREGEXP` for pattern matching → [Text and Pattern Functions](TEXT-AND-PATTERN-FUNCTIONS.md)
- **Boolean operators**: All spatial operators return boolean values and **should be used with `= TRUE` or `= FALSE` in DQL**

## 📚 Function and Operator Categories

This library provides comprehensive PostgreSQL function and operator support organized into the following categories:

### **🔗 Array and JSON Functions**
Complete documentation for array manipulation and JSON/JSONB operations.
- **[Array and JSON Functions and Operators](ARRAY-AND-JSON-FUNCTIONS.md)**
- Includes: Array operators (`@>`, `<@`, `&&`), JSON operators (`->`, `->>`, `#>`, `#>>`), array functions, JSON functions, JSONB functions

### **🗺️ PostGIS Spatial Functions**
Complete documentation for PostGIS spatial operations and geometry processing.
- **[PostGIS Spatial Functions and Operators](SPATIAL-FUNCTIONS-AND-OPERATORS.md)**
- Includes: Bounding box operators, distance operators, spatial relationship functions, measurement functions, overlay functions, geometry processing functions

### **📝 Text and Pattern Functions**
Complete documentation for text processing, pattern matching, and regular expressions.
- **[Text and Pattern Functions and Operators](TEXT-AND-PATTERN-FUNCTIONS.md)**
- Includes: Text operators (`~`, `ilike`, `@@`), regular expression functions, text processing functions, full-text search functions

### **📅 Date and Range Functions**
Complete documentation for date/time operations and range type functions.
- **[Date, Time, and Range Functions](DATE-AND-RANGE-FUNCTIONS.md)**
- Includes: Date/time functions, range creation functions, range operators, temporal operations

### **🔢 Mathematical Functions**
Complete documentation for mathematical operations and utility functions.
- **[Mathematical and Utility Functions](MATHEMATICAL-FUNCTIONS.md)**
- Includes: Mathematical functions, type conversion functions, formatting functions, utility functions

### **🌳 Ltree Functions**
Complete documentation for PostgreSQL ltree (label tree) operations and hierarchical data processing.
- **[Ltree Functions](LTREE-TYPE.md)**
- Includes: Path manipulation functions, ancestor/descendant operations, type conversion functions

## 🚀 Quick Reference

### Most Commonly Used Functions

**Array Operations:** ([Complete documentation](ARRAY-AND-JSON-FUNCTIONS.md))
- `CONTAINS` (`@>`) - Test if array/range contains elements
- `OVERLAPS` (`&&`) - Test if arrays/ranges overlap
- `ARRAY_AGG` - Aggregate values into arrays

**JSON Operations:** ([Complete documentation](ARRAY-AND-JSON-FUNCTIONS.md))
- `JSON_GET_FIELD_AS_TEXT` (`->>`) - Extract JSON field as text
- `JSON_BUILD_OBJECT` - Build JSON objects
- `JSONB_PATH_EXISTS` - Test JSON path existence

**Spatial Operations:** ([Complete documentation](SPATIAL-FUNCTIONS-AND-OPERATORS.md))
- `ST_INTERSECTS` - Test if geometries intersect
- `ST_DISTANCE` - Calculate distance between geometries
- `ST_CONTAINS` - Test spatial containment

**Text Operations:** ([Complete documentation](TEXT-AND-PATTERN-FUNCTIONS.md))
- `CASEFOLD` - Advanced case-insensitive text comparison with Unicode support
- `ILIKE` - Case-insensitive pattern matching
- `REGEXP` (`~`) - Regular expression matching
- `STARTS_WITH` - Test if text starts with substring

**Date/Range Operations:** ([Complete documentation](DATE-AND-RANGE-FUNCTIONS.md))
- `DATE_ADD` - Add interval to date
- `DATE_EXTRACT` - Extract date components
- `DATERANGE` - Create date ranges

**Mathematical Operations:** ([Complete documentation](MATHEMATICAL-FUNCTIONS.md))
- `GAMMA` - Gamma function for statistical calculations
- `GREATEST`/`LEAST` - Find maximum/minimum values
- `LGAMMA` - Natural logarithm of gamma function
- `ROUND` - Round numeric values
- `RANDOM` - Generate random numbers

**Ltree Operations:** ([Complete documentation](LTREE-TYPE.md))
- `SUBLTREE` - Extract subpath from ltree
- `SUBPATH` - Extract subpath with offset and length
- `NLEVEL` - Get number of labels in path
- `INDEX` - Find position of ltree in another ltree
- `LCA` - Find longest common ancestor

**Utility Operations:**
- `CRC32` - CRC-32 checksum computation
- `CRC32C` - CRC-32C checksum computation
- `REVERSE_BYTES` - Reverse byte order for bytea values
- `UUID_EXTRACT_TIMESTAMP` - Extract timestamp from UUID v1 or v7
- `UUID_EXTRACT_VERSION` - Extract version number from UUID
- `UUIDV4` - Explicit UUID version 4 generation
- `UUIDV7` - Generate timestamp-ordered UUIDs (version 7) for better database performance

## 📋 Summary of Available Function Categories

### **Array & JSON Functions**
- **Array Operations**: Manipulate PostgreSQL arrays (append, remove, replace, shuffle)
- **JSON Functions**: Work with JSON/JSONB data types
- **JSONB Path Functions**: Advanced JSONB querying with path expressions

### **Spatial Functions (PostGIS)**
- **Relationship Functions**: Test spatial relationships between geometries
- **Measurement Functions**: Calculate distances, areas, lengths, and angles
- **Overlay Functions**: Perform geometric operations (intersection, union, difference)
- **Processing Functions**: Transform, simplify, and modify geometries

### **Text & Pattern Functions**
- **Regexp Functions**: Pattern matching and replacement
- **Text Functions**: String manipulation and searching
- **Full-Text Search**: PostgreSQL's text search capabilities

### **Date & Range Functions**
- **Date Operations**: Add/subtract dates, extract components
- **Range Types**: Create and work with various range types
- **Overlap Testing**: Check if date ranges overlap

### **Mathematical Functions**
- **Basic Math**: Power, square root, trigonometric functions
- **Aggregation**: Array and JSON aggregation functions
- **Utility Functions**: Random numbers, rounding, type casting

### **Ltree Functions**
- **Path Operations**: Extract subpaths, manipulate hierarchical paths
- **Ancestor Operations**: Find common ancestors, calculate path levels
- **Type Conversion**: Convert between ltree and text types

### **Operators**
- **Array Operators**: Contains, overlaps, element testing
- **Spatial Operators**: Bounding box and distance operations
- **Text Operators**: Pattern matching and concatenation

---

**💡 Tips for Usage:**
1. **Boolean functions** should be used with `= TRUE` or `= FALSE` in DQL → [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md)
2. **Spatial functions** work best with proper geometry types and indexes → [Spatial Types](SPATIAL-TYPES.md)
3. **Array functions** provide efficient PostgreSQL array operations → [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md)
4. **JSON functions** support both JSON and JSONB data types → [Array and JSON Functions](ARRAY-AND-JSON-FUNCTIONS.md)
5. **Range functions** provide efficient storage and querying for value ranges → [Range Types](RANGE-TYPES.md)
6. **Mathematical functions** work with numeric types and return appropriate precision → [Mathematical Functions](MATHEMATICAL-FUNCTIONS.md)
7. **Ltree functions** provide efficient hierarchical data operations and path manipulation → [Ltree Functions](LTREE-TYPE.md)

---

**📖 For More Information:**
- [Available Types](AVAILABLE-TYPES.md) - PostgreSQL data types supported by this library
- [Value Objects for Range Types](RANGE-TYPES.md) - Working with range value objects
- [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md) - Practical examples and patterns
- [Spatial Types](SPATIAL-TYPES.md) - PostGIS geometry and geography types
- [Geometry Arrays](GEOMETRY-ARRAYS.md) - Working with arrays of geometric types
