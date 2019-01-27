<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonObjectKeysTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_OBJECT_KEYS' => JsonObjectKeys::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT json_object_keys(c0_.object) AS sclr_0 FROM ContainsJson c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT JSON_OBJECT_KEYS(e.object) FROM %s e', ContainsJson::class),
        ];
    }
}
