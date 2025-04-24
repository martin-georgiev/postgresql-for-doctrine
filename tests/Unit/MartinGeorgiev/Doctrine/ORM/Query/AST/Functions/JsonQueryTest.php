<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'extracts all array items' => "SELECT json_query(c0_.object1, '$.items[*]') AS sclr_0 FROM ContainsJsons c0_",
            'extracts nested object' => "SELECT json_query(c0_.object1, '$.address') AS sclr_0 FROM ContainsJsons c0_",
            'extracts all authors from books array' => "SELECT json_query(c0_.object1, '$.store.book[*].author') AS sclr_0 FROM ContainsJsons c0_",
            'extracts specific range of books' => "SELECT json_query(c0_.object1, '$.store.book[0 to 2]') AS sclr_0 FROM ContainsJsons c0_",
            'filters books by price condition' => "SELECT json_query(c0_.object1, '$.store.book[*]?(@.price > 10)') AS sclr_0 FROM ContainsJsons c0_",
            // Additional scenarios based on PostgreSQL's json_query capabilities
            'extracts last array element' => "SELECT json_query(c0_.object1, '$.items[last]') AS sclr_0 FROM ContainsJsons c0_",
            'extracts specific keys from objects in array' => "SELECT json_query(c0_.object1, '$.users[*]?(@.active == true).name') AS sclr_0 FROM ContainsJsons c0_",
            'extracts nested array with multiple conditions' => "SELECT json_query(c0_.object1, '$.store.book[*]?(@.price < 30 && @.category == \"fiction\")') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts all array items' => \sprintf("SELECT JSON_QUERY(e.object1, '$.items[*]') FROM %s e", ContainsJsons::class),
            'extracts nested object' => \sprintf("SELECT JSON_QUERY(e.object1, '$.address') FROM %s e", ContainsJsons::class),
            'extracts all authors from books array' => \sprintf("SELECT JSON_QUERY(e.object1, '$.store.book[*].author') FROM %s e", ContainsJsons::class),
            'extracts specific range of books' => \sprintf("SELECT JSON_QUERY(e.object1, '$.store.book[0 to 2]') FROM %s e", ContainsJsons::class),
            'filters books by price condition' => \sprintf("SELECT JSON_QUERY(e.object1, '$.store.book[*]?(@.price > 10)') FROM %s e", ContainsJsons::class),
            // Additional scenarios based on PostgreSQL's json_query capabilities
            'extracts last array element' => \sprintf("SELECT JSON_QUERY(e.object1, '$.items[last]') FROM %s e", ContainsJsons::class),
            'extracts specific keys from objects in array' => \sprintf("SELECT JSON_QUERY(e.object1, '$.users[*]?(@.active == true).name') FROM %s e", ContainsJsons::class),
            'extracts nested array with multiple conditions' => \sprintf("SELECT JSON_QUERY(e.object1, '$.store.book[*]?(@.price < 30 && @.category == \"fiction\")') FROM %s e", ContainsJsons::class),
        ];
    }
}
