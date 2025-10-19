<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FlaggedRegexpLike;

class FlaggedRegexpLikeTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FLAGGED_REGEXP_LIKE' => FlaggedRegexpLike::class, // @phpstan-ignore-line
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'checks if text matches regex pattern with flags' => "SELECT regexp_like(c0_.text1, 'pattern', 'i') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if text matches regex pattern with flags' => \sprintf("SELECT FLAGGED_REGEXP_LIKE(e.text1, 'pattern', 'i') FROM %s e", ContainsTexts::class),
        ];
    }
}
