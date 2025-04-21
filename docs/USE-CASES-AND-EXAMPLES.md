Clarification on usage of `ILIKE`, `CONTAINS`, `IS_CONTAINED_BY`, `DATE_OVERLAPS` and some other operator-like functions
---

`Error: Expected =, <, <=, <>, >, >=, !=, got 'ILIKE'"` (or similar) is probably one of the most common DQL errors you may experience when working with this library. The cause for is that when parsing the DQL Doctrine won't recognize `ILIKE` as a known operator. In fact `ILIKE` is registered as a boolean function.
Doctrine doesn't provide easy support for implementing custom operators. This may change in the future but for now it is easier to trick the DQL parser with a boolean expression.

Example intent with PostgreSQL:
```sql
SELECT * FROM emails WHERE subject ILIKE 'Test email';
```

Intuitively, one may assume the below DQL. However it will not work:
```sql
SELECT e
FROM EmailEntity e
WHERE e.subject ILIKE 'Test email'
```

The correct DQL is with a boolean expression that will parse correctly and can look like this:
```sql
SELECT e
FROM EmailEntity e
WHERE ILIKE(e.subject, 'Test email') = TRUE
```

Using JSON_BUILD_OBJECT and JSONB_BUILD_OBJECT
---

These functions currently only support string literals and object references as arguments. Here are some valid examples:

```sql
-- Basic usage with string literals and entity properties
SELECT JSON_BUILD_OBJECT('name', e.userName, 'email', e.userEmail) FROM User e

-- Multiple key-value pairs
SELECT JSONB_BUILD_OBJECT('id', e.id, 'status', 'active', 'type', e.userType) FROM Employee e

-- Invalid usage (will not work):
SELECT JSON_BUILD_OBJECT('count', COUNT(*))  -- Aggregate functions not supported
SELECT JSONB_BUILD_OBJECT('number', 123)     -- All number types, NULL and boolean values not supported currently
```

Note: Keys must always be string literals, while values can be either string literals or object property references.

Using JSON Path Functions
---

PostgreSQL 14+ introduced JSON path functions that provide a powerful way to query JSON data. Here are some examples:

```sql
-- Check if a JSON path exists with a condition
SELECT e FROM Entity e WHERE JSONB_PATH_EXISTS(e.jsonData, '$.items[*] ? (@.price > 100)') = TRUE

-- Check if a JSON path matches a condition
SELECT e FROM Entity e WHERE JSONB_PATH_MATCH(e.jsonData, 'exists($.items[*] ? (@.price >= 50 && @.price <= 100))') = TRUE

-- Extract all items matching a path query
SELECT e.id, JSONB_PATH_QUERY(e.jsonData, '$.items[*].name') FROM Entity e

-- Extract all items as an array
SELECT e.id, JSONB_PATH_QUERY_ARRAY(e.jsonData, '$.items[*].id') FROM Entity e

-- Extract the first item matching a path query
SELECT e.id, JSONB_PATH_QUERY_FIRST(e.jsonData, '$.items[*] ? (@.featured == true)') FROM Entity e
```

Using Regular Expression Functions
---

PostgreSQL 15+ introduced additional regular expression functions that provide more flexibility when working with text data:

```sql
-- Count occurrences of a pattern
SELECT e.id, REGEXP_COUNT(e.text, '\d{3}-\d{2}-\d{4}') as ssn_count FROM Entity e

-- Find position of a pattern
SELECT e.id, REGEXP_INSTR(e.text, 'important') as position FROM Entity e

-- Extract substring matching a pattern
SELECT e.id, REGEXP_SUBSTR(e.text, 'https?://[\w.-]+') as url FROM Entity e
```

Using Date Functions
---

PostgreSQL 14+ introduced additional date functions that provide more flexibility when working with dates and timestamps:

```sql
-- Bin timestamps into 15-minute intervals
SELECT DATE_BIN('15 minutes', e.createdAt, '2001-01-01') FROM Entity e

-- Add an interval to a timestamp (timezone parameter is optional)
SELECT DATE_ADD(e.timestampWithTz, '1 day') FROM Entity e
SELECT DATE_ADD(e.timestampWithTz, '1 day', 'Europe/London') FROM Entity e

-- Subtract an interval from a timestamp (timezone parameter is optional)
SELECT DATE_SUBTRACT(e.timestampWithTz, '2 hours') FROM Entity e
SELECT DATE_SUBTRACT(e.timestampWithTz, '2 hours', 'UTC') FROM Entity e
```
