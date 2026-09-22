<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XpathExists;
use PHPUnit\Framework\Attributes\Test;

final class XpathExistsTest extends XmlTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'XPATH_EXISTS' => XpathExists::class,
        ];
    }

    #[Test]
    public function returns_false_when_the_xpath_has_no_match_in_a_literal(): void
    {
        $dql = "SELECT XPATH_EXISTS('//missing', '<root><child>text</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_when_the_xpath_matches_an_entity_field(): void
    {
        $dql = 'SELECT XPATH_EXISTS(\'//item\', t.content) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsXml t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
