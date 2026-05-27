<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XmlExists;
use PHPUnit\Framework\Attributes\Test;

final class XmlExistsTest extends XmlTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XMLEXISTS' => XmlExists::class,
        ];
    }

    #[Test]
    public function returns_true_when_xpath_matches_literal(): void
    {
        $dql = "SELECT XMLEXISTS('//item', '<root><item>foo</item></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue((bool) $result[0]['result']);
    }

    #[Test]
    public function returns_false_when_xpath_has_no_match_in_literal(): void
    {
        $dql = "SELECT XMLEXISTS('//missing', '<root><item>foo</item></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse((bool) $result[0]['result']);
    }

    #[Test]
    public function returns_true_when_xpath_matches_entity_property(): void
    {
        $dql = 'SELECT XMLEXISTS(\'//item\', t.content) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsXml t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue((bool) $result[0]['result']);
    }
}
