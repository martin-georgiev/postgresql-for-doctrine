<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FlaggedRegexpReplace;

class FlaggedRegexpReplaceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FLAGGED_REGEXP_REPLACE' => FlaggedRegexpReplace::class, // @phpstan-ignore-line
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
            \sprintf("SELECT FLAGGED_REGEXP_REPLACE(e.text1, 'pattern', 'replacement', 'g') FROM %s e", ContainsTexts::class),
        ];
    }
}
