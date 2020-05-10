<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsArrays;

class AnyTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ANY_OF' => Any::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT c0_.id AS id_0 FROM ContainsArrays c0_ WHERE c0_.id > ANY(c0_.array1)',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT e.id FROM %s e WHERE e.id > ANY_OF(e.array1)', ContainsArrays::class),
        ];
    }
}
