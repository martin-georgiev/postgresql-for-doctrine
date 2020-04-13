<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsTexts;

class JsonbAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_AGG' => JsonbAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT json_agg(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_AGG(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
