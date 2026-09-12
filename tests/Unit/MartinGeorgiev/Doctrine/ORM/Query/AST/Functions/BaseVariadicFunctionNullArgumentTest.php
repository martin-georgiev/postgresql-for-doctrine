<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestNewValueArgumentFunction;

final class BaseVariadicFunctionNullArgumentTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TEST_NEW_VALUE_ARGUMENT' => TestNewValueArgumentFunction::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'literal NULL on a NewValue-mapped argument becomes SQL NULL' => 'SELECT test_new_value_argument(c0_.text1, NULL) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'literal NULL on a NewValue-mapped argument becomes SQL NULL' => \sprintf('SELECT TEST_NEW_VALUE_ARGUMENT(e.text1, NULL) FROM %s e', ContainsTexts::class),
        ];
    }
}
