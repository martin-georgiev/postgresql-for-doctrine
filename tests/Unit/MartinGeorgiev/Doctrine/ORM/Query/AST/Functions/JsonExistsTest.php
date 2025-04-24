<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'checks if simple path exists' => "SELECT json_exists(c0_.object1, '$.name') AS sclr_0 FROM ContainsJsons c0_",
            'checks if nested path exists' => "SELECT json_exists(c0_.object1, '$.address.city') AS sclr_0 FROM ContainsJsons c0_",
            'checks if array element exists' => "SELECT json_exists(c0_.object1, '$.items[0]') AS sclr_0 FROM ContainsJsons c0_",
            'checks if deeply nested array element exists' => "SELECT json_exists(c0_.object1, '$.users[0].addresses[0].street') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if simple path exists' => \sprintf("SELECT JSON_EXISTS(e.object1, '$.name') FROM %s e", ContainsJsons::class),
            'checks if nested path exists' => \sprintf("SELECT JSON_EXISTS(e.object1, '$.address.city') FROM %s e", ContainsJsons::class),
            'checks if array element exists' => \sprintf("SELECT JSON_EXISTS(e.object1, '$.items[0]') FROM %s e", ContainsJsons::class),
            'checks if deeply nested array element exists' => \sprintf("SELECT JSON_EXISTS(e.object1, '$.users[0].addresses[0].street') FROM %s e", ContainsJsons::class),
        ];
    }
}
