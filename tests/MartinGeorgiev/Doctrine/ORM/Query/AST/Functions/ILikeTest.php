<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ILike;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class ILikeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ILIKE' => ILike::class,
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
            sprintf("SELECT ILIKE(e.text,'TEST') FROM %s e", ContainsText::class),
        ];
    }
}
