<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Right;

class RightTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RIGHT' => Right::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'gets right substring' => 'SELECT right(c0_.text1, 6) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets right substring' => \sprintf('SELECT RIGHT(e.text1, 6) FROM %s e', ContainsTexts::class),
        ];
    }
}
