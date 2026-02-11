<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Reverse;

class ReverseTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REVERSE' => Reverse::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'reverses text' => 'SELECT reverse(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'reverses text' => \sprintf('SELECT REVERSE(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
