<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Unaccent;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

class UnaccentTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UNACCENT' => Unaccent::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT unaccent(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'SELECT unaccent(\'unaccent\', c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT UNACCENT(e.text1) FROM %s e', ContainsTexts::class),
            \sprintf('SELECT UNACCENT(\'unaccent\', e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
