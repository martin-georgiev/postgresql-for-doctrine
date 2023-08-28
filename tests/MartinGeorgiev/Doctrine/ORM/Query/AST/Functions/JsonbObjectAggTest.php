<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg;

class JsonbObjectAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_OBJECT_AGG' => JsonbObjectAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT jsonb_object_agg(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_OBJECT_AGG(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }
}
