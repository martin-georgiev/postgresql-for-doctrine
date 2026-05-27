# XML Functions

> 📖 **See also**: [Available Types](AVAILABLE-TYPES.md) for the `xml` and `xml[]` DBAL types

| PostgreSQL function | Register for DQL as | Implemented by |
|---|---|---|
| xml_is_well_formed | XML_IS_WELL_FORMED | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlIsWellFormed` |
| xmlagg | XML_AGG | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg` |
| xmlcomment | XMLCOMMENT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xmlcomment` |
| xmlconcat | XMLCONCAT | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xmlconcat` |
| xpath | XPATH | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xpath` |
| xpath_exists | XPATH_EXISTS | `MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XpathExists` |

## Usage Examples

```sql
-- Aggregate XML values (supports ORDER BY)
SELECT e.category, XML_AGG(e.xmlData ORDER BY e.createdAt) FROM App\Entity\Article e GROUP BY e.category

-- Validate XML well-formedness
SELECT XML_IS_WELL_FORMED(e.xmlData) FROM App\Entity\Article e

-- Create an XML comment
SELECT XMLCOMMENT('Generated at ' || NOW()) FROM App\Entity\Article e WHERE e.id = :id

-- Concatenate two XML fragments
SELECT XMLCONCAT(e.header, e.body) FROM App\Entity\Article e WHERE e.id = :id

-- Concatenate three XML fragments
SELECT XMLCONCAT(e.header, e.body, e.footer) FROM App\Entity\Article e WHERE e.id = :id

-- Extract all matching nodes as an XML array
SELECT XPATH('//item/title/text()', e.xmlData) FROM App\Entity\Article e WHERE e.id = :id

-- Check whether an XPath expression matches any node
SELECT XPATH_EXISTS('//item[@active="true"]', e.xmlData) FROM App\Entity\Article e WHERE e.id = :id
```
