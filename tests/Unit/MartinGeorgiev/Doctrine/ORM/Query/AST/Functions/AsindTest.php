<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asind;

class AsindTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASIND' => Asind::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT ASIND(1) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT ASIND(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ASIND(1) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT ASIND(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
