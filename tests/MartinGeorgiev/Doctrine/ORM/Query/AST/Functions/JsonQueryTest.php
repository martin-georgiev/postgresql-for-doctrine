<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonQuery;

class JsonQueryTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_QUERY' => JsonQuery::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT json_query(c0_.object1, '$.items[*]') AS sclr_0 FROM ContainsJsons c0_",
            "SELECT json_query(c0_.object1, '$.address') AS sclr_0 FROM ContainsJsons c0_",
            // Additional test cases for important scenarios
            "SELECT json_query(c0_.object1, '$.store.book[*].author') AS sclr_0 FROM ContainsJsons c0_",
            "SELECT json_query(c0_.object1, '$.store.book[0 to 2]') AS sclr_0 FROM ContainsJsons c0_",
            "SELECT json_query(c0_.object1, '$.store.book[*]?(@.price > 10)') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSON_QUERY(e.object1, '$.items[*]') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_QUERY(e.object1, '$.address') FROM %s e", ContainsJsons::class),
            // Additional test cases matching the SQL statements above
            \sprintf("SELECT JSON_QUERY(e.object1, '$.store.book[*].author') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_QUERY(e.object1, '$.store.book[0 to 2]') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSON_QUERY(e.object1, '$.store.book[*]?(@.price > 10)') FROM %s e", ContainsJsons::class),
        ];
    }
}
