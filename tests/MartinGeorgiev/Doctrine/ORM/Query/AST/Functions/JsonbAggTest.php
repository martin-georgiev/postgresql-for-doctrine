<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbAgg;

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
            'SELECT jsonb_agg(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_AGG(e.text1) FROM %s e', ContainsTexts::class),
        ];
    }
}
