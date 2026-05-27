<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlConcat;
use PHPUnit\Framework\Attributes\Test;

final class XmlConcatTest extends XmlTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLCONCAT' => XmlConcat::class,
        ];
    }

    #[Test]
    public function concatenates_two_xml_values(): void
    {
        $dql = "SELECT XMLCONCAT('<a>1</a>', '<b>2</b>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<a>1</a><b>2</b>', $result[0]['result']);
    }

    #[Test]
    public function concatenates_three_xml_values(): void
    {
        $dql = "SELECT XMLCONCAT('<a>hello</a>', '<b>world</b>', '<c>!</c>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<a>hello</a><b>world</b><c>!</c>', $result[0]['result']);
    }

    #[Test]
    public function concatenates_xml_values_with_entity_property(): void
    {
        $dql = "SELECT XMLCONCAT('<prefix/>', t.content) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('<prefix/><root><item>foo</item><item>bar</item></root>', $result[0]['result']);
    }
}
