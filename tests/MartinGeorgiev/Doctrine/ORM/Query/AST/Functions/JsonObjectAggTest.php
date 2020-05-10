<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsTexts;

class JsonObjectAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_OBJECT_AGG' => JsonObjectAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT json_object_agg(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_OBJECT_AGG(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
