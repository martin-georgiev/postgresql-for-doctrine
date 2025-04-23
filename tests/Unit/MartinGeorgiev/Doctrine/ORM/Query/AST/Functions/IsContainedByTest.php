<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy;

class IsContainedByTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IS_CONTAINED_BY' => IsContainedBy::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.array1 <@ '{681,1185,1878}') AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT IS_CONTAINED_BY(e.array1, '{681,1185,1878}') FROM %s e", ContainsArrays::class),
        ];
    }
}
