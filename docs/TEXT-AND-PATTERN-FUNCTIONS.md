# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> Text and pattern functions and operators

This page covers PostgreSQL [text processing](https://www.postgresql.org/docs/18/functions-string.html), [pattern matching](https://www.postgresql.org/docs/18/functions-matching.html), and regular expression functions and operators available in this library.

> **See also:** [Common use cases and examples](USE-CASES-AND-EXAMPLES.md) for these functions in whole queries

## Text and pattern operators

Some PostgreSQL operators have multiple meanings depending on the data types involved. This library provides specific DQL function names to avoid conflicts:

| Operator | Array/JSON usage | Spatial usage | Text/pattern usage |
|---|---|---|---|
| `~` | N/A | `SPATIAL_CONTAINS` (bounding box contains) | `REGEXP` (text pattern matching) |

**Usage Guidelines:**
- **Text**: Use `REGEXP`, `IREGEXP` for pattern matching
- **Boolean operators**: All operators return boolean values and **should be used with `= TRUE` or `= FALSE` in DQL** (see [The DQL dialect](DQL-DIALECT.md#boolean-functions-need-a-comparison))

### Text and pattern operators

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

## Regular expression functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| regexp_count | REGEXP_COUNT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpCount` |
| regexp_instr | REGEXP_INSTR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpInstr` |
| regexp_like | REGEXP_LIKE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike` |
| regexp_match | REGEXP_MATCH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch` |
| regexp_replace | REGEXP_REPLACE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace` |
| regexp_substr | REGEXP_SUBSTR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpSubstr` |

## Text processing functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| ascii | ASCII | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ascii` |
| btrim | BTRIM | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Btrim` |
| casefold | CASEFOLD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Casefold` |
| char_length | CHAR_LENGTH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CharLength` |
| chr | CHR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Chr` |
| concat_ws | CONCAT_WS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ConcatWs` |
| decode | DECODE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Decode` |
| encode | ENCODE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Encode` |
| format | FORMAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Format` |
| initcap | INITCAP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Initcap` |
| left | LEFT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Left` |
| lpad | LPAD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lpad` |
| ltrim | LTRIM | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltrim` |
| octet_length | OCTET_LENGTH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\OctetLength` |
| quote_ident | QUOTE_IDENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteIdent` |
| quote_literal | QUOTE_LITERAL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteLiteral` |
| quote_nullable | QUOTE_NULLABLE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\QuoteNullable` |
| repeat | REPEAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Repeat` |
| reverse | REVERSE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Reverse` |
| right | RIGHT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Right` |
| rpad | RPAD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rpad` |
| rtrim | RTRIM | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rtrim` |
| split_part | SPLIT_PART | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\SplitPart` |
| starts_with | STARTS_WITH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StartsWith` |
| strpos | STRPOS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strpos` |
| string_agg | STRING_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringAgg` |
| string_to_array | STRING_TO_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray` |
| translate | TRANSLATE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Translate` |
| unaccent | UNACCENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent` |

## Full-text search functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| array_to_tsvector | ARRAY_TO_TSVECTOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToTsvector` |
| json_to_tsvector | JSON_TO_TSVECTOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonToTsvector` |
| phraseto_tsquery | PHRASETO_TSQUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PhrasetoTsquery` |
| plainto_tsquery | PLAINTO_TSQUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PlaintoTsquery` |
| setweight | SETWEIGHT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Setweight` |
| strip | STRIP | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strip` |
| to_tsquery | TO_TSQUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery` |
| to_tsvector | TO_TSVECTOR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector` |
| ts_headline | TS_HEADLINE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsHeadline` |
| ts_rank | TS_RANK | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRank` |
| ts_rank_cd | TS_RANK_CD | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsRankCd` |
| tsquery_phrase | TSQUERY_PHRASE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsqueryPhrase` |
| tsvector_to_array | TSVECTOR_TO_ARRAY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TsvectorToArray` |
| websearch_to_tsquery | WEBSEARCH_TO_TSQUERY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WebsearchToTsquery` |

## Fuzzy string matching functions (fuzzystrmatch extension)

> **Requires the [`fuzzystrmatch`](https://www.postgresql.org/docs/18/fuzzystrmatch.html) extension:** `CREATE EXTENSION IF NOT EXISTS fuzzystrmatch;`

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| daitch_mokotoff | DAITCH_MOKOTOFF | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DaitchMokotoff` |
| difference | DIFFERENCE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Difference` |
| dmetaphone | DMETAPHONE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Dmetaphone` |
| dmetaphone_alt | DMETAPHONE_ALT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\DmetaphoneAlt` |
| levenshtein | LEVENSHTEIN | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Levenshtein` |
| levenshtein_less_equal | LEVENSHTEIN_LESS_EQUAL | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\LevenshteinLessEqual` |
| metaphone | METAPHONE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Metaphone` |
| soundex | SOUNDEX | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Soundex` |

## Trigram similarity functions (pg_trgm extension)

> **Requires the [`pg_trgm`](https://www.postgresql.org/docs/18/pgtrgm.html) extension:** `CREATE EXTENSION IF NOT EXISTS pg_trgm;`

### Similarity functions

| PostgreSQL function | Register for DQL as | Implemented by |
|---|---|---|
| similarity | SIMILARITY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\Similarity` |
| word_similarity | WORD_SIMILARITY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\WordSimilarity` |
| strict_word_similarity | STRICT_WORD_SIMILARITY | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\StrictWordSimilarity` |

### Similarity threshold operators

| PostgreSQL operator | Register for DQL as | Implemented by |
|---|---|---|
| % | ARE_SIMILAR | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\AreSimilar` |
| <% | IS_WORD_SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\IsWordSimilarTo` |
| %> | CONTAINS_WORD_SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ContainsWordSimilarTo` |
| <<% | IS_STRICT_WORD_SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\IsStrictWordSimilarTo` |
| %>> | CONTAINS_STRICT_WORD_SIMILAR_TO | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ContainsStrictWordSimilarTo` |

### Distance operators

| PostgreSQL operator | Register for DQL as | Implemented by |
|---|---|---|
| <-> | SIMILARITY_DISTANCE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\SimilarityDistance` |
| <<-> | WORD_SIMILARITY_DISTANCE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\WordSimilarityDistance` |
| <->> | REVERSE_WORD_SIMILARITY_DISTANCE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ReverseWordSimilarityDistance` |
| <<<-> | STRICT_WORD_SIMILARITY_DISTANCE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\StrictWordSimilarityDistance` |
| <->>> | REVERSE_STRICT_WORD_SIMILARITY_DISTANCE | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Trgm\ReverseStrictWordSimilarityDistance` |

## Hashing and checksum functions

| PostgreSQL functions | Register for DQL as | Implemented by |
|---|---|---|
| crc32 | CRC32 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32` |
| crc32c | CRC32C | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Crc32c` |
| md5 | MD5 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Md5` |
| reverse (bytea) | REVERSE_BYTES | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReverseBytes` |
| sha224 | SHA224 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha224` |
| sha256 | SHA256 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha256` |
| sha384 | SHA384 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha384` |
| sha512 | SHA512 | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sha512` |

## Usage examples

The results come from these rows of `Entity`:

| id | email | text | content | category | name |
|---|---|---|---|---|---|
| 1 | ada@example.com | Released on 2026-09-27 | The search engine ranks the search terms | books | Dune |
| 2 | not-an-email | No date here | A search for other terms | books | Neuromancer |
| 3 | miles@jazz.example.org | Recorded 1959-03-02 | Nothing relevant | music | Kind of Blue |

Each `-- →` line under a query is one row of `getResult()`, measured on PostgreSQL 18 with PHP 8.5, DBAL 4.5 and ORM 3.7 and the session time zone set to UTC; [What comes back: hydration](HYDRATION.md) explains the PHP types. A query without its own `ORDER BY` returns rows in no set order; the results list them in the order of the sample rows, or by group after a `GROUP BY`.

```sql
-- REGEXP_LIKE with a real-world email pattern - POSIX syntax, not SQL LIKE syntax
SELECT e FROM Entity e WHERE REGEXP_LIKE(e.email, '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$') = TRUE
-- → [Entity {id: 1}, Entity {id: 3}]

-- REGEXP_MATCH returns an array of capture groups (one element per group)
SELECT REGEXP_MATCH(e.text, '([0-9]{4})-([0-9]{2})-([0-9]{2})') as date_parts FROM Entity e
-- → ['date_parts' => '{2026,09,27}']
--   ['date_parts' => null]
--   ['date_parts' => '{1959,03,02}']

-- Full-text search: combine TO_TSVECTOR + TO_TSQUERY; must use = TRUE in DQL WHERE clauses
SELECT e FROM Entity e WHERE TSMATCH(TO_TSVECTOR(e.content), TO_TSQUERY('search & terms')) = TRUE
-- → [Entity {id: 1}, Entity {id: 2}]

-- TS_RANK: sort by relevance score - smaller values are less relevant
SELECT e, TS_RANK(TO_TSVECTOR(e.content), TO_TSQUERY('search')) as rank FROM Entity e ORDER BY rank DESC
-- → [0 => Entity {id: 1}, 'rank' => 0.075990885]
--   [0 => Entity {id: 2}, 'rank' => 0.06079271]
--   [0 => Entity {id: 3}, 'rank' => 0.0]

-- STRING_AGG with separator - requires GROUP BY
SELECT e.category, STRING_AGG(e.name, ', ' ORDER BY e.name) as names FROM Entity e GROUP BY e.category
-- → ['category' => 'books', 'names' => 'Dune, Neuromancer']
--   ['category' => 'music', 'names' => 'Kind of Blue']
```

**Tips:**
- `REGEXP`, `IREGEXP` and the `REGEXP_*` functions take [POSIX regular expressions](https://www.postgresql.org/docs/18/functions-matching.html#FUNCTIONS-POSIX-REGEXP), not `LIKE` patterns.
- `ILIKE` is `LIKE` without case sensitivity.
- Full-text search stays fast on large tables with a [GIN index](https://www.postgresql.org/docs/18/textsearch-indexes.html) on the `tsvector`.
- `UNACCENT` requires the [`unaccent`](https://www.postgresql.org/docs/18/unaccent.html) extension: `CREATE EXTENSION IF NOT EXISTS unaccent;`
- `STRING_AGG` takes the separator as its second argument and an `ORDER BY` inside the call.
