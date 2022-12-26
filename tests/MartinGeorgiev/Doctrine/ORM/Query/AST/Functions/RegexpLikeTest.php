<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;
use Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class RegexpLikeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_LIKE' => RegexpLike::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT regexp_like(c0_.text1, 'pattern') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT REGEXP_LIKE(e.text1, 'pattern') FROM %s e", ContainsTexts::class),
        ];
    }
}
