<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJsons;

class JsonbStripNullsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_STRIP_NULLS' => JsonbStripNulls::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT jsonb_strip_nulls(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_STRIP_NULLS(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
