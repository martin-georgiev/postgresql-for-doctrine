<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike;

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
