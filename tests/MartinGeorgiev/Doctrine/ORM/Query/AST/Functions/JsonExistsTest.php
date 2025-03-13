<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists;

class JsonExistsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_EXISTS' => JsonExists::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            // Basic usage
            "SELECT json_exists(c0_.object1, '$.name') AS sclr_0 FROM ContainsJsons c0_",
            // Nested path
            "SELECT json_exists(c0_.object1, '$.address.city') AS sclr_0 FROM ContainsJsons c0_",
            // Array element
            "SELECT json_exists(c0_.object1, '$.items[0]') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_EXISTS(e.object1, '$.name') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_EXISTS(e.object1, '$.address.city') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_EXISTS(e.object1, '$.items[0]') FROM %s e", ContainsJsons::class),
        ];
    }
}
