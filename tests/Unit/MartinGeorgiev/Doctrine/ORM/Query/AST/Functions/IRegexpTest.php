<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IRegexp;

class IRegexpTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IREGEXP' => IRegexp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'case-insensitive regex pattern matching' => "SELECT (c0_.text1 ~* '.*Thomas.*') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'case-insensitive regex pattern matching' => \sprintf("SELECT IREGEXP(e.text1, '.*Thomas.*') FROM %s e", ContainsTexts::class),
        ];
    }
}
