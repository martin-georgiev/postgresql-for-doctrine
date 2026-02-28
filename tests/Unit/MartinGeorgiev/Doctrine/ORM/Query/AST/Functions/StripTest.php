<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Strip;

class StripTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STRIP' => Strip::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT strip(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT STRIP(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
