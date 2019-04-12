Common errors when using ILIKE, CONTAINS, IS_CONTAINED_BY and other operator-like functions
---

`Error: Expected =, <, <=, <>, >, >=, !=, got 'ILIKE'"` (or similar) is probably one of the most common errors you may experience when working with this library. The cause for is that when parsing the DQL Doctrine won't recognise `ILIKE` as a known operator. In fact `ILIKE` is registered as a function.
Doctrine doesn't provide easy support for implementing custom operators. This may change in the future but for now it is far easier to trick the DQL parser.

Example intend with DQL:
```sql
    SELECT e
    FROM EmailEntity e
    WHERE e.subject ILIKE 'Test email'
```

Correct DQL that will parse:
```sql
    SELECT e
    FROM EmailEntity e
    WHERE ILIKE(e.subject, 'Test email') = TRUE
```
