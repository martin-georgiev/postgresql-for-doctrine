<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotRegexp;

class NotRegexpTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NOT_REGEXP' => NotRegexp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.text1 !~ '.*thomas.*') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT NOT_REGEXP(e.text1, '.*thomas.*') FROM %s e", ContainsTexts::class),
        ];
    }
}
