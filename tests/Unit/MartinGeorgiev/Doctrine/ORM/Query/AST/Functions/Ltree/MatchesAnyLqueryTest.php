<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesAnyLquery;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class MatchesAnyLqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MATCHES_ANY_LQUERY' => MatchesAnyLquery::class,
            'ARR' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'matches path against an array of lquery patterns' => "SELECT (c0_.text1 ?? CAST(ARRAY['Top.*', 'A.*'] AS lquery[])) AS sclr_0 FROM ContainsTexts c0_",
            'matches path against a single-element array of lquery patterns' => "SELECT (c0_.text1 ?? CAST(ARRAY['Top.*'] AS lquery[])) AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'matches path against an array of lquery patterns' => \sprintf("SELECT MATCHES_ANY_LQUERY(e.text1, ARR('Top.*', 'A.*')) FROM %s e", ContainsTexts::class),
            'matches path against a single-element array of lquery patterns' => \sprintf("SELECT MATCHES_ANY_LQUERY(e.text1, ARR('Top.*')) FROM %s e", ContainsTexts::class),
        ];
    }
}
