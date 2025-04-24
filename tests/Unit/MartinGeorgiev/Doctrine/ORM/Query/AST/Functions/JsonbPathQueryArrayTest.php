<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryArray;

class JsonbPathQueryArrayTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new JsonbPathQueryArray('JSONB_PATH_QUERY_ARRAY');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_QUERY_ARRAY' => JsonbPathQueryArray::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts array with condition' => "SELECT jsonb_path_query_array(c0_.object1, '$.a[*] ? (@ > 2)') AS sclr_0 FROM ContainsJsons c0_",
            'extracts array of items' => "SELECT jsonb_path_query_array(c0_.object1, '$.items[*].id') AS sclr_0 FROM ContainsJsons c0_",
            'extracts array of items with vars argument' => "SELECT jsonb_path_query_array(c0_.object1, '$.items[*].id', '{\"strict\": false}') AS sclr_0 FROM ContainsJsons c0_",
            'extracts array of items with vars and silent arguments' => "SELECT jsonb_path_query_array(c0_.object1, '$.items[*].id', '{\"strict\": false}', 'true') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts array with condition' => \sprintf("SELECT JSONB_PATH_QUERY_ARRAY(e.object1, '$.a[*] ? (@ > 2)') FROM %s e", ContainsJsons::class),
            'extracts array of items' => \sprintf("SELECT JSONB_PATH_QUERY_ARRAY(e.object1, '$.items[*].id') FROM %s e", ContainsJsons::class),
            'extracts array of items with vars argument' => \sprintf("SELECT JSONB_PATH_QUERY_ARRAY(e.object1, '$.items[*].id', '{\"strict\": false}') FROM %s e", ContainsJsons::class),
            'extracts array of items with vars and silent arguments' => \sprintf("SELECT JSONB_PATH_QUERY_ARRAY(e.object1, '$.items[*].id', '{\"strict\": false}', 'true') FROM %s e", ContainsJsons::class),
        ];
    }

    public function test_invalid_boolean_throws_exception(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for jsonb_path_query_array. Must be "true" or "false".');

        $dql = \sprintf("SELECT JSONB_PATH_QUERY_ARRAY(e.object1, '$.items[*].id', '{\"strict\": false}', 'invalid') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_path_query_array() requires at least 2 arguments');

        $dql = \sprintf('SELECT JSONB_PATH_QUERY_ARRAY(e.object1) FROM %s e', ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_path_query_array() requires between 2 and 4 arguments');

        $dql = \sprintf("SELECT JSONB_PATH_QUERY_ARRAY(e.object1, '$.items[*].id', '{\"strict\": false}', 'true', 'extra_arg') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
