<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp;

class NotIRegexpTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NOT_IREGEXP' => NotIRegexp::class,
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
            \sprintf("SELECT NOT_IREGEXP(e.text1, '.*Thomas.*') FROM %s e", ContainsTexts::class),
        ];
    }
}
