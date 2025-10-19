<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor;

class FloorTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FLOOR' => Floor::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'rounds decimal down to nearest integer' => 'SELECT FLOOR(c0_.decimal1) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'rounds decimal down to nearest integer' => \sprintf('SELECT FLOOR(e.decimal1) FROM %s e', ContainsDecimals::class),
        ];
    }
}
