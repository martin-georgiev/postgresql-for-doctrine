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
    public function evaluates_xpath_and_returns_matched_nodes(): void
    {
        $dql = "SELECT XPATH('//child/text()', '<root><child>hello</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{hello}', $result[0]['result']);
    }

    #[Test]
    public function evaluates_xpath_and_returns_empty_array_for_no_match(): void
    {
        $dql = "SELECT XPATH('//missing', '<root><child>hello</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{}', $result[0]['result']);
    }

    #[Test]
    public function evaluates_xpath_with_entity_property(): void
    {
        $dql = "SELECT XPATH('//item/text()', t.content) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{foo,bar}', $result[0]['result']);
    }
}
