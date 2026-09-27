# <picture><source media="(prefers-color-scheme: dark)" srcset="assets/logo-dark.svg"><img src="assets/logo.svg" alt="" width="32" height="32" align="absmiddle"></picture> XML functions

> **See also:** [Available types](AVAILABLE-TYPES.md) for the `xml` and `xml[]` DBAL types

| [PostgreSQL function](https://www.postgresql.org/docs/18/functions-xml.html) | Register for DQL as | Implemented by |
|---|---|---|
| xml_is_well_formed | XML_IS_WELL_FORMED | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormed` |
| xml_is_well_formed_content | XML_IS_WELL_FORMED_CONTENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedContent` |
| xml_is_well_formed_document | XML_IS_WELL_FORMED_DOCUMENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedDocument` |
| xmlagg | XMLAGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg` |
| xmlcomment | XMLCOMMENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlComment` |
| xmlconcat | XMLCONCAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlConcat` |
| xmlexists | XMLEXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlExists` |
| xmlpi | XMLPI | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlPi` |
| xmltext | XMLTEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlText` |
| xpath | XPATH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xpath` |
| xpath_exists | XPATH_EXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XpathExists` |

## Usage examples

The results come from these rows of `App\Entity\Article`, read with PostgreSQL's default `xmloption`, `content`:

| id | category | xmlData | xmlText | createdAt |
|---|---|---|---|---|
| 1 | news | `<feed><item active="true"><title>First</title></item><item><title>Second</title></item></feed>` | `<feed><item/></feed>` | 2026-09-01 08:00 |
| 2 | news | `<feed><item><title>Third</title></item></feed>` | `<item/><item/>` | 2026-09-02 08:00 |
| 3 | blog | `<feed/>` | `plain text` | 2026-09-03 08:00 |

Each `-- →` line under a query is one row of `getResult()`, measured on PostgreSQL 18 with PHP 8.5, DBAL 4.5 and ORM 3.7 and the session time zone set to UTC; [What comes back: hydration](HYDRATION.md) explains the PHP types. A query without its own `ORDER BY` returns rows in no set order; the results list them in the order of the sample rows, or by group after a `GROUP BY`.

```sql
-- ORDER BY inside the aggregate - not obvious in Doctrine DQL
SELECT e.category, XMLAGG(e.xmlData ORDER BY e.createdAt) FROM App\Entity\Article e GROUP BY e.category
-- → ['category' => 'blog', 1 => '<feed/>']
--   ['category' => 'news', 1 => '<feed><item active="true"><title>First</title></item><item><title>Second</title></item></feed><feed><item><title>Third</title></item></feed>']

-- xml_is_well_formed uses the session xmloption (DOCUMENT or CONTENT mode)
-- xml_is_well_formed_document always requires a single root element
-- xml_is_well_formed_content accepts fragments and plain text nodes
SELECT XML_IS_WELL_FORMED(e.xmlText) FROM App\Entity\Article e
-- → [1 => true]
--   [1 => true]
--   [1 => true]
SELECT XML_IS_WELL_FORMED_DOCUMENT(e.xmlText) FROM App\Entity\Article e
-- → [1 => true]
--   [1 => false]
--   [1 => false]
SELECT XML_IS_WELL_FORMED_CONTENT(e.xmlText) FROM App\Entity\Article e
-- → [1 => true]
--   [1 => true]
--   [1 => true]

-- XPath text() node extraction and attribute predicates
SELECT XPATH('//item/title/text()', e.xmlData) FROM App\Entity\Article e WHERE e.id = :id
-- → [1 => '{First,Second}']
SELECT XPATH_EXISTS('//item[@active="true"]', e.xmlData) FROM App\Entity\Article e WHERE e.id = :id
-- → [1 => true]

-- XPath existence test (boolean) and XML processing instruction
SELECT XMLEXISTS('//item', e.xmlData) FROM App\Entity\Article e WHERE e.id = :id
-- → [1 => true]
SELECT XMLPI('php', 'echo "hello";') FROM App\Entity\Article e WHERE e.id = :id
-- → [1 => '<?php echo "hello";?>']
```
