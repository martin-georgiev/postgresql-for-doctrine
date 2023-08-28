<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSetLax;

class JsonbSetLaxTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_SET_LAX' => JsonbSetLax::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT jsonb_set_lax(c0_.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJsons c0_",
            "SELECT jsonb_set_lax(c0_.object1, '{country}', null) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSONB_SET_LAX(e.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSONB_SET_LAX(e.object1, '{country}', null) FROM %s e", ContainsJsons::class),
        ];
    }
}
