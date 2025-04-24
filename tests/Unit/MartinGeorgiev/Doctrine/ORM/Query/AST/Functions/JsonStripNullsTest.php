<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls;

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
