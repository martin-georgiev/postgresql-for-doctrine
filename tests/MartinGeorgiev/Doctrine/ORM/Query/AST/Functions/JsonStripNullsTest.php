<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonStripNullsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_STRIP_NULLS' => JsonStripNulls::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT json_strip_nulls(c0_.object) AS sclr_0 FROM ContainsJson c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_STRIP_NULLS(e.object) FROM %s e', ContainsJson::class),
        ];
    }
}
