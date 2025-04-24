<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys;

class JsonbObjectKeysTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_OBJECT_KEYS' => JsonbObjectKeys::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT jsonb_object_keys(c0_.object1) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSONB_OBJECT_KEYS(e.object1) FROM %s e', ContainsJsons::class),
        ];
    }
}
