<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpMatch;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

class RegexpMatchTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_MATCH' => RegexpMatch::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT regexp_match(c0_.text1, 'pattern') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT REGEXP_MATCH(e.text1, 'pattern') FROM %s e", ContainsTexts::class),
        ];
    }
}
