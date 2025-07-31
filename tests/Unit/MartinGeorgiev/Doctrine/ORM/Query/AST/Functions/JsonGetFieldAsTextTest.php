<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetFieldAsText;

class JsonGetFieldAsTextTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_FIELD' => JsonGetField::class,
            'JSON_GET_FIELD_AS_TEXT' => JsonGetFieldAsText::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts field as text' => "SELECT (c0_.object1 ->> 'country') AS sclr_0 FROM ContainsJsons c0_",
            'extracts array element as text' => 'SELECT (c0_.object1 ->> 0) AS sclr_0 FROM ContainsJsons c0_',
            'extracts nested array element as text' => "SELECT ((c0_.object1 -> 'tags') ->> 1) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts field as text' => \sprintf("SELECT JSON_GET_FIELD_AS_TEXT(e.object1, 'country') FROM %s e", ContainsJsons::class),
            'extracts array element as text' => \sprintf('SELECT JSON_GET_FIELD_AS_TEXT(e.object1, 0) FROM %s e', ContainsJsons::class),
            'extracts nested array element as text' => \sprintf("SELECT JSON_GET_FIELD_AS_TEXT(JSON_GET_FIELD(e.object1, 'tags'), 1) FROM %s e", ContainsJsons::class),
        ];
    }
}
