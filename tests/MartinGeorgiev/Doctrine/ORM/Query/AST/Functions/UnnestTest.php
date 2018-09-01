<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unnest;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class UnnestTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UNNEST' => Unnest::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT unnest(c0_.array) AS sclr_0 FROM ContainsArray c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf('SELECT UNNEST(e.array) FROM %s e', ContainsArray::class),
        ];
    }
}
