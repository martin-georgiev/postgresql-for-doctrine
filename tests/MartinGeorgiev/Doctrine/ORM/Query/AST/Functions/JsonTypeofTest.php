<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonTypeof;

class JsonTypeofTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_TYPEOF' => JsonTypeof::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT json_typeof(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_TYPEOF(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
