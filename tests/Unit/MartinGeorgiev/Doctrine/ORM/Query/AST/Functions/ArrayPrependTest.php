<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend;

class ArrayPrependTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_PREPEND' => ArrayPrepend::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'prepends string element to array' => "SELECT array_prepend('new-value', c0_.array1) AS sclr_0 FROM ContainsArrays c0_",
            'prepends numeric element to array' => 'SELECT array_prepend(42, c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
            'prepends element using parameter' => 'SELECT array_prepend(?, c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
            'prepends null to array' => 'SELECT array_prepend(null, c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'prepends string element to array' => \sprintf("SELECT ARRAY_PREPEND('new-value', e.array1) FROM %s e", ContainsArrays::class),
            'prepends numeric element to array' => \sprintf('SELECT ARRAY_PREPEND(42, e.array1) FROM %s e', ContainsArrays::class),
            'prepends element using parameter' => \sprintf('SELECT ARRAY_PREPEND(:dql_parameter, e.array1) FROM %s e', ContainsArrays::class),
            'prepends null to array' => \sprintf('SELECT ARRAY_PREPEND(NULL, e.array1) FROM %s e', ContainsArrays::class),
        ];
    }
}
