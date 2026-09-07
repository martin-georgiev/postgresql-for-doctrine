<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLtxtquery;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class MatchesLtxtqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MATCHES_LTXTQUERY' => MatchesLtxtquery::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'matches path against an ltxtquery literal' => "SELECT (c0_.text1 @ CAST('Top & !Child2' AS ltxtquery)) AS sclr_0 FROM ContainsTexts c0_",
            'matches path against an ltxtquery column' => 'SELECT (c0_.text1 @ CAST(c0_.text2 AS ltxtquery)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'matches path against an ltxtquery literal' => \sprintf("SELECT MATCHES_LTXTQUERY(e.text1, 'Top & !Child2') FROM %s e", ContainsTexts::class),
            'matches path against an ltxtquery column' => \sprintf('SELECT MATCHES_LTXTQUERY(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
