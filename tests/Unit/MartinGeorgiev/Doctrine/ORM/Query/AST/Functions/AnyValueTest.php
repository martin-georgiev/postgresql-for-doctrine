<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyValue;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;

class AnyValueTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ANY_VALUE' => AnyValue::class,
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'from array field' => 'SELECT any_value(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
            'from text field' => 'SELECT any_value(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'from list of integers' => "SELECT any_value(ARRAY['red', 'green', 'blue']) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'from array field' => \sprintf('SELECT ANY_VALUE(e.array1) FROM %s e', ContainsArrays::class),
            'from text field' => \sprintf('SELECT ANY_VALUE(e.text1) FROM %s e', ContainsTexts::class),
            'from list of integers' => \sprintf("SELECT ANY_VALUE(ARRAY('red', 'green', 'blue')) FROM %s e", ContainsTexts::class),
        ];
    }
}
