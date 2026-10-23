<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlSerialize;
use PHPUnit\Framework\Attributes\Test;

final class XmlSerializeTest extends XmlTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLSERIALIZE' => XmlSerialize::class,
        ];
    }

    #[Test]
    public function returns_the_serialized_xml_from_a_literal(): void
    {
        $dql = "SELECT XMLSERIALIZE(CONTENT '<a/>' AS TEXT) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<a/>', $result[0]['result']);
    }

    #[Test]
    public function returns_the_serialized_xml_from_an_entity_field(): void
    {
        $dql = 'SELECT XMLSERIALIZE(DOCUMENT t.content AS VARCHAR) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsXml t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<root><item>foo</item><item>bar</item></root>', $result[0]['result']);
    }

    #[Test]
    public function returns_the_serialized_xml_with_indent(): void
    {
        $dql = 'SELECT XMLSERIALIZE(DOCUMENT t.content AS TEXT INDENT) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsXml t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        // Older PostgreSQL minor releases (e.g. 16.4) end the indented output with a newline; later ones do not
        $this->assertMatchesRegularExpression('#^<root>\n  <item>foo</item>\n  <item>bar</item>\n</root>\n?\z#', $result[0]['result']);
    }
}
