<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlAgg;
use PHPUnit\Framework\Attributes\Test;

final class XmlAggTest extends XmlTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XML_AGG' => XmlAgg::class,
        ];
    }

    #[Test]
    public function returns_the_aggregated_values_from_an_entity_field(): void
    {
        $dql = 'SELECT XML_AGG(t.content) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsXml t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('<root><item>foo</item><item>bar</item></root>', $result[0]['result']);
    }

    #[Test]
    public function returns_the_aggregated_values_with_an_order_by_clause(): void
    {
        $dql = 'SELECT XML_AGG(t.content ORDER BY t.id DESC) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsXml t
                WHERE t.id IN (1, 2)';

        $result = $this->executeDqlQuery($dql);
        $expected = '<catalog><product><name>test</name></product></catalog><root><item>foo</item><item>bar</item></root>';
        $this->assertSame($expected, $result[0]['result']);
    }
}
