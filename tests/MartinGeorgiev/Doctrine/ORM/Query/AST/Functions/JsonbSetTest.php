<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSet;

class JsonbSetTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_SET' => JsonbSet::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT jsonb_set(c0_.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSONB_SET(e.object1, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') FROM %s e", ContainsJsons::class),
        ];
    }
}
