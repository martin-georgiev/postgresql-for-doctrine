<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ltree\MatchesLquery;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

final class MatchesLqueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MATCHES_LQUERY' => MatchesLquery::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'matches path against an lquery literal' => "SELECT (c0_.text1 ~ CAST('Top.*' AS lquery)) AS sclr_0 FROM ContainsTexts c0_",
            'matches path against an lquery column' => 'SELECT (c0_.text1 ~ CAST(c0_.text2 AS lquery)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'matches path against an lquery literal' => \sprintf("SELECT MATCHES_LQUERY(e.text1, 'Top.*') FROM %s e", ContainsTexts::class),
            'matches path against an lquery column' => \sprintf('SELECT MATCHES_LQUERY(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
