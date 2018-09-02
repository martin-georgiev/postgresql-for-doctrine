<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

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
            'SELECT c0_.id AS id_0 FROM ContainsArray c0_ WHERE c0_.id > ANY(c0_.array)',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            sprintf('SELECT e.id FROM %s e WHERE e.id > ANY_OF(e.array)', ContainsArray::class),
        ];
    }
}
