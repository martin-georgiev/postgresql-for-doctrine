<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Uuidv7;

class Uuidv7Test extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'UUIDV7' => Uuidv7::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates uuid v7' => 'SELECT uuidv7() AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates uuid v7' => \sprintf('SELECT UUIDV7() FROM %s e', ContainsTexts::class),
        ];
    }
}
