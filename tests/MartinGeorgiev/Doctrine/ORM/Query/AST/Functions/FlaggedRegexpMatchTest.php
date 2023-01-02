<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FlaggedRegexpMatch;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

class FlaggedRegexpMatchTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FLAGGED_REGEXP_MATCH' => FlaggedRegexpMatch::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT regexp_match(c0_.text1, 'pattern', 'i') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT FLAGGED_REGEXP_MATCH(e.text1, 'pattern', 'i') FROM %s e", ContainsTexts::class),
        ];
    }
}
