<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random;

class RandomTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANDOM' => Random::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates random number' => 'SELECT RANDOM() AS sclr_0 FROM ContainsDecimals c0_',
            'uses random in arithmetic expression' => 'SELECT RANDOM() + c0_.decimal1 AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates random number' => \sprintf('SELECT RANDOM() FROM %s e', ContainsDecimals::class),
            'uses random in arithmetic expression' => \sprintf('SELECT RANDOM() + e.decimal1 FROM %s e', ContainsDecimals::class),
        ];
    }
}
