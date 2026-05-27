<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\XpathExists;

final class XpathExistsTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): XpathExists
    {
        return new XpathExists('XPATH_EXISTS');
    }

    protected function getStringFunctions(): array
    {
        return [
            'XPATH_EXISTS' => XpathExists::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks xpath existence with two arguments' => 'SELECT xpath_exists(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'checks xpath existence with namespace array' => 'SELECT xpath_exists(c0_.text1, c0_.text2, c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks xpath existence with two arguments' => \sprintf('SELECT XPATH_EXISTS(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'checks xpath existence with namespace array' => \sprintf('SELECT XPATH_EXISTS(e.text1, e.text2, e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
