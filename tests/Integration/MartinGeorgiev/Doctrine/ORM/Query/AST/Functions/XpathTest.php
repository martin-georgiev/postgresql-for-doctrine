<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xpath;
use PHPUnit\Framework\Attributes\Test;

final class XpathTest extends XmlTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XPATH' => Xpath::class,
        ];
    }

    #[Test]
    public function returns_the_matched_nodes_from_a_literal(): void
    {
        $dql = "SELECT XPATH('//child/text()', '<root><child>hello</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{hello}', $result[0]['result']);
    }

    #[Test]
    public function returns_the_matched_nodes_from_an_entity_field(): void
    {
        $dql = "SELECT XPATH('//item/text()', t.content) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{foo,bar}', $result[0]['result']);
    }
}
