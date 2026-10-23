<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitCount;

final class BitCountTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BIT_COUNT' => BitCount::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'counts the set bits of a field' => 'SELECT bit_count(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'counts the set bits of a field' => \sprintf('SELECT BIT_COUNT(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
