<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XpathExists;
use PHPUnit\Framework\Attributes\Test;

final class XpathExistsTest extends TextTestCase
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
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue((bool) $result[0]['result']);
    }

    #[Test]
    public function returns_false_for_nonexistent_xpath(): void
    {
        $dql = "SELECT XPATH_EXISTS('//missing', '<root><child>text</child></root>') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse((bool) $result[0]['result']);
    }
}
