<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class IlikeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ILIKE' => Ilike::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT c0_.text ilike 'TEST' AS sclr_0 FROM ContainsText c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT ILIKE(e.text,'TEST') FROM %s e", ContainsText::class),
        ];
    }
}
