<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Difference;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class DifferenceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DIFFERENCE' => Difference::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT difference(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT DIFFERENCE(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
