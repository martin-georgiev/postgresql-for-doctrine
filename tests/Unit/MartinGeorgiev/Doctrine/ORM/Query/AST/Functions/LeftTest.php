<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Left;

class LeftTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEFT' => Left::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'gets left substring' => 'SELECT left(c0_.text1, 4) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets left substring' => \sprintf('SELECT LEFT(e.text1, 4) FROM %s e', ContainsTexts::class),
        ];
    }
}
