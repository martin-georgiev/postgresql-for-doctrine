<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToTsvector;

class ArrayToTsvectorTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_TSVECTOR' => ArrayToTsvector::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
           'SELECT array_to_tsvector(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT ARRAY_TO_TSVECTOR(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
