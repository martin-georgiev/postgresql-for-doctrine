# Text and Pattern Functions and Operators

This document covers PostgreSQL text processing, pattern matching, and regular expression functions and operators available in this library.

> 📖 **See also**: [Common Use Cases and Examples](USE-CASES-AND-EXAMPLES.md) for practical text processing and regular expression examples

## Text and Pattern Operators

**⚠️ Important**: Some PostgreSQL operators have multiple meanings depending on the data types involved. This library provides specific DQL function names to avoid conflicts:

| Operator | Array/JSON Usage | Spatial Usage | Text/Pattern Usage |
|---|---|---|---|
| `~` | N/A | `SPATIAL_CONTAINS` (bounding box contains) | `REGEXP` (text pattern matching) |

**Usage Guidelines:**
- **Text**: Use `REGEXP`, `IREGEXP` for pattern matching
- **Boolean operators**: All operators return boolean values and **should be used with `= TRUE` or `= FALSE` in DQL**

### Text and Pattern Operators

| PostgreSQL operator | Register for DQL as | Implemented by |
|---|---|---|
| ilike | ILIKE ([Usage note](USE-CASES-AND-EXAMPLES.md)) | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike` |
| similar to | SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SimilarTo` |
| not similar to | NOT_SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotSimilarTo` |
| ~ | REGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Regexp` |
| ~* | IREGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp` |
| !~ | NOT_REGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotRegexp` |
| !~* | NOT_IREGEXP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp` |
| @@ | TSMATCH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch` |
| \|\| | STRCONCAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StrConcat` |

## Regular Expression Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| regexp_count | REGEXP_COUNT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount` |
| regexp_instr | REGEXP_INSTR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr` |
| regexp_like | REGEXP_LIKE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike` |
| regexp_match | REGEXP_MATCH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch` |
| regexp_replace | REGEXP_REPLACE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace` |
| regexp_substr | REGEXP_SUBSTR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSubstr` |

## Text Processing Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| split_part | SPLIT_PART | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart` |
| starts_with | STARTS_WITH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith` |
| string_agg | STRING_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg` |
| string_to_array | STRING_TO_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray` |
| unaccent | UNACCENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent` |

## Full-Text Search Functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| to_tsquery | TO_TSQUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery` |
| to_tsvector | TO_TSVECTOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector` |
| websearch_to_tsquery | WEBSEARCH_TO_TSQUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WebsearchToTsquery` |

## Usage Examples

```sql
-- Text and pattern matching
-- Case-insensitive pattern matching
SELECT e FROM Entity e WHERE IREGEXP(e.name, '^admin.*') = TRUE

-- Extract text using regex
SELECT e, REGEXP_SUBSTR(e.description, 'version [0-9.]+') as version FROM Entity e

-- Replace text patterns
SELECT e, REGEXP_REPLACE(e.content, 'old_pattern', 'new_pattern') as updated_content FROM Entity e

-- Check if text starts with specific string
SELECT e FROM Entity e WHERE STARTS_WITH(e.name, 'user_') = TRUE

-- Full-text search
SELECT e FROM Entity e WHERE TSMATCH(e.search_vector, 'query & terms') = TRUE

-- Count pattern occurrences
SELECT e, REGEXP_COUNT(e.text, '[0-9]+') as number_count FROM Entity e

-- Find position of pattern
SELECT e, REGEXP_INSTR(e.text, 'error') as error_position FROM Entity e

-- Test if pattern matches
SELECT e FROM Entity e WHERE REGEXP_LIKE(e.email, '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$') = TRUE

-- Extract first match
SELECT e, REGEXP_MATCH(e.text, '([0-9]{4})-([0-9]{2})-([0-9]{2})') as date_parts FROM Entity e

-- Split text into parts
SELECT e, SPLIT_PART(e.full_name, ' ', 1) as first_name,
       SPLIT_PART(e.full_name, ' ', 2) as last_name FROM Entity e

-- String concatenation
SELECT e, STRCONCAT(e.first_name, ' ', e.last_name) as full_name FROM Entity e

-- Remove accents from text
SELECT e, UNACCENT(e.name) as normalized_name FROM Entity e

-- Aggregate strings
SELECT e.category, STRING_AGG(e.name, ', ') as names FROM Entity e GROUP BY e.category

-- Convert string to array
SELECT e, STRING_TO_ARRAY(e.tags, ',') as tag_array FROM Entity e

-- Case-insensitive LIKE
SELECT e FROM Entity e WHERE ILIKE(e.name, '%admin%') = TRUE

-- SQL pattern matching
SELECT e FROM Entity e WHERE SIMILAR_TO(e.code, '[A-Z]{2}[0-9]{4}') = TRUE

-- Negated pattern matching
SELECT e FROM Entity e WHERE NOT_SIMILAR_TO(e.code, '[A-Z]{2}[0-9]{4}') = TRUE

-- Full-text search setup
SELECT e FROM Entity e WHERE TSMATCH(TO_TSVECTOR(e.content), TO_TSQUERY('search & terms')) = TRUE
```

**💡 Tips for Usage:**
1. **Boolean operators** should be used with `= TRUE` or `= FALSE` in DQL
2. **Regular expressions** use PostgreSQL's POSIX regular expression syntax
3. **Full-text search** requires proper text search configuration and indexes
4. **ILIKE** provides case-insensitive pattern matching similar to LIKE
5. **UNACCENT** requires the unaccent extension to be installed in PostgreSQL
6. **String aggregation** with STRING_AGG allows custom separators and ordering
