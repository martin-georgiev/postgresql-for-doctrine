<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson;

class RowToJsonTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ROW_TO_JSON' => RowToJson::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts row to json' => 'SELECT row_to_json(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'converts row with expression to json' => 'SELECT row_to_json(UPPER(c0_.text1)) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts row to json' => \sprintf('SELECT ROW_TO_JSON(e.text1) FROM %s e', ContainsTexts::class),
            'converts row with expression to json' => \sprintf('SELECT ROW_TO_JSON(UPPER(e.text1)) FROM %s e', ContainsTexts::class),
        ];
    }
}
