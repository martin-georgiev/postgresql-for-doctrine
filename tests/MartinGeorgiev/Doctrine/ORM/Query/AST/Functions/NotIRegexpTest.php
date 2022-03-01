<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

class NotIRegexpTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NOTIREGEXP' => NotIRegexp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.text1 !~* '.*Thomas.*') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT NOTIREGEXP(e.text1, '.*Thomas.*') FROM %s e", ContainsTexts::class),
        ];
    }
}
