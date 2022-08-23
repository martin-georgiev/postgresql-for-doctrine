<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ColumnToJsonb;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

class ColumnToJsonbTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COLUMN_TO_JSONB' => ColumnToJsonb::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT to_jsonb(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT COLUMN_TO_JSONB(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
