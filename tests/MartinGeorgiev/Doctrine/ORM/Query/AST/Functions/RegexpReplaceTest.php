<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace;

class RegexpReplaceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_REPLACE' => RegexpReplace::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT regexp_replace(c0_.text1, 'pattern', 'replacement', 'g') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT REGEXP_REPLACE(e.text1, 'pattern', 'replacement', 'g') FROM %s e", ContainsTexts::class),
        ];
    }
}
