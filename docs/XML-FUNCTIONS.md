# XML Functions

> 📖 **See also**: [Available Types](AVAILABLE-TYPES.md) for the `xml` and `xml[]` DBAL types

| PostgreSQL function | Register for DQL as | Implemented by |
|---|---|---|
| xml_is_well_formed | XML_IS_WELL_FORMED | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormed` |
| xml_is_well_formed_content | XML_IS_WELL_FORMED_CONTENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedContent` |
| xml_is_well_formed_document | XML_IS_WELL_FORMED_DOCUMENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormedDocument` |
| xmlagg | XMLAGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg` |
| xmlcomment | XMLCOMMENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xmlcomment` |
| xmlconcat | XMLCONCAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xmlconcat` |
| xmltext | XMLTEXT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xmltext` |
| xpath | XPATH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xpath` |
| xpath_exists | XPATH_EXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XpathExists` |

## Usage Examples

```sql
-- ORDER BY inside the aggregate — not obvious in Doctrine DQL
SELECT e.category, XMLAGG(e.xmlData ORDER BY e.createdAt) FROM App\Entity\Article e GROUP BY e.category

-- xml_is_well_formed uses the session xmloption (DOCUMENT or CONTENT mode)
-- xml_is_well_formed_document always requires a single root element
-- xml_is_well_formed_content accepts fragments and plain text nodes
SELECT XML_IS_WELL_FORMED(e.xmlData) FROM App\Entity\Article e
SELECT XML_IS_WELL_FORMED_DOCUMENT(e.xmlData) FROM App\Entity\Article e
SELECT XML_IS_WELL_FORMED_CONTENT(e.xmlData) FROM App\Entity\Article e

-- XPath text() node extraction and attribute predicates
SELECT XPATH('//item/title/text()', e.xmlData) FROM App\Entity\Article e WHERE e.id = :id
SELECT XPATH_EXISTS('//item[@active="true"]', e.xmlData) FROM App\Entity\Article e WHERE e.id = :id
```
