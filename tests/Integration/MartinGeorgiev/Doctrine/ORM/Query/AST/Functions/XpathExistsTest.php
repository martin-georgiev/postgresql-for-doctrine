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
    public function returns_true_for_matching_xpath(): void
    {
        $dql = "SELECT XPATH_EXISTS('//root', '<root><child>text</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_for_nonexistent_xpath(): void
    {
        $dql = "SELECT XPATH_EXISTS('//missing', '<root><child>text</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsXml t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function returns_true_for_matching_xpath_with_entity_property(): void
    {
        $dql = 'SELECT XPATH_EXISTS(\'//item\', t.content) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsXml t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
