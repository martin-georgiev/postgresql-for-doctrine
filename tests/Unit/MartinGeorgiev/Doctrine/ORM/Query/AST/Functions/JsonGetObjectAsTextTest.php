<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObjectAsText;

class JsonGetObjectAsTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_OBJECT_AS_TEXT' => JsonGetObjectAsText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts top-level object as text' => "SELECT (c0_.object1 #>> '{metadata}') AS sclr_0 FROM ContainsJsons c0_",
            'extracts nested object as text' => "SELECT (c0_.object1 #>> '{user,address}') AS sclr_0 FROM ContainsJsons c0_",
            'extracts array element as text' => "SELECT (c0_.object1 #>> '{items,0}') AS sclr_0 FROM ContainsJsons c0_",
            'extracts deeply nested object as text' => "SELECT (c0_.object1 #>> '{store,departments,main}') AS sclr_0 FROM ContainsJsons c0_",
            'extracts from complex path as text' => "SELECT (c0_.object1 #>> '{data,users,0,profile}') AS sclr_0 FROM ContainsJsons c0_",
            'extracts last array element as text' => "SELECT (c0_.object1 #>> '{records,-1}') AS sclr_0 FROM ContainsJsons c0_",
            'extracts nested array element as text' => "SELECT (c0_.object1 #>> '{categories,2,description}') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts top-level object as text' => \sprintf("SELECT JSON_GET_OBJECT_AS_TEXT(e.object1, '{metadata}') FROM %s e", ContainsJsons::class),
            'extracts nested object as text' => \sprintf("SELECT JSON_GET_OBJECT_AS_TEXT(e.object1, '{user,address}') FROM %s e", ContainsJsons::class),
            'extracts array element as text' => \sprintf("SELECT JSON_GET_OBJECT_AS_TEXT(e.object1, '{items,0}') FROM %s e", ContainsJsons::class),
            'extracts deeply nested object as text' => \sprintf("SELECT JSON_GET_OBJECT_AS_TEXT(e.object1, '{store,departments,main}') FROM %s e", ContainsJsons::class),
            'extracts from complex path as text' => \sprintf("SELECT JSON_GET_OBJECT_AS_TEXT(e.object1, '{data,users,0,profile}') FROM %s e", ContainsJsons::class),
            'extracts last array element as text' => \sprintf("SELECT JSON_GET_OBJECT_AS_TEXT(e.object1, '{records,-1}') FROM %s e", ContainsJsons::class),
            'extracts nested array element as text' => \sprintf("SELECT JSON_GET_OBJECT_AS_TEXT(e.object1, '{categories,2,description}') FROM %s e", ContainsJsons::class),
        ];
    }
}
