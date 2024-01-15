Clarification on usage of `ILIKE`, `CONTAINS`, `IS_CONTAINED_BY`, `DATE_OVERLAPS` and some other operator-like functions
---

`Error: Expected =, <, <=, <>, >, >=, !=, got 'ILIKE'"` (or similar) is probably one of the most common DQL errors you may experience when working with this library. The cause for is that when parsing the DQL Doctrine won't recognise `ILIKE` as a known operator. In fact `ILIKE` is registered as a boolean function.
Doctrine doesn't provide easy support for implementing custom operators. This may change in the future but for now it is easier to trick the DQL parser with a boolean expression.

Example intent with DQL:
```sql
SELECT e
FROM EmailEntity e
WHERE e.subject ILIKE 'Test email'
```

Boolean expression that will parse and is equavelnt to teh above DQL:
```sql
SELECT e
FROM EmailEntity e
WHERE ILIKE(e.subject, 'Test email') = TRUE
```
