<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsJsons;

class JsonbPrettyTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PRETTY' => JsonbPretty::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT jsonb_pretty(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
            "SELECT jsonb_pretty('{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_PRETTY(e.object1) FROM %s e', ContainsJsons::class),
            \sprintf("SELECT JSONB_PRETTY('{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJsons::class),
        ];
    }
}
