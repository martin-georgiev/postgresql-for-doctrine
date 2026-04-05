<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RandomNormal;

class RandomNormalTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANDOM_NORMAL' => RandomNormal::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT RANDOM_NORMAL() AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT RANDOM_NORMAL(0, 1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT RANDOM_NORMAL() FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT RANDOM_NORMAL(0, 1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
