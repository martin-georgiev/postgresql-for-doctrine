<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Xpath;

class XpathTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Xpath('XPATH');
    }

    protected function getStringFunctions(): array
    {
        return [
            'XPATH' => Xpath::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'evaluates xpath with two arguments' => 'SELECT xpath(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'evaluates xpath with namespace array' => 'SELECT xpath(c0_.text1, c0_.text2, c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'evaluates xpath with two arguments' => \sprintf('SELECT XPATH(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'evaluates xpath with namespace array' => \sprintf('SELECT XPATH(e.text1, e.text2, e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
