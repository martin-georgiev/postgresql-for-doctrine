<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls;
use Tests\MartinGeorgiev\Doctrine\Fixtures\Entity\ContainsJsons;

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
            'SELECT json_strip_nulls(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_STRIP_NULLS(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
