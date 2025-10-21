<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Uuidv4;

class Uuidv4Test extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UUIDV4' => Uuidv4::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates uuid v4' => 'SELECT uuidv4() AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates uuid v4' => \sprintf('SELECT UUIDV4() FROM %s e', ContainsTexts::class),
        ];
    }
}
