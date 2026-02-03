<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbTypeof;

class JsonbTypeofTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_TYPEOF' => JsonbTypeof::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'gets typeof jsonb field' => 'SELECT jsonb_typeof(c0_.jsonbObject1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'gets typeof jsonb field' => \sprintf('SELECT JSONB_TYPEOF(e.jsonbObject1) FROM %s e', ContainsJsons::class),
        ];
    }
}
