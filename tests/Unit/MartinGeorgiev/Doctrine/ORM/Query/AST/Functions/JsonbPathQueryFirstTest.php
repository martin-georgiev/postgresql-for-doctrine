<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPathQueryFirst;

class JsonbPathQueryFirstTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new JsonbPathQueryFirst('JSONB_PATH_QUERY_FIRST');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PATH_QUERY_FIRST' => JsonbPathQueryFirst::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts first item with condition' => "SELECT jsonb_path_query_first(c0_.object1, '$.items[*] ? (@.price > 100)') AS sclr_0 FROM ContainsJsons c0_",
            'extracts first item from path' => "SELECT jsonb_path_query_first(c0_.object1, '$.items[*].id') AS sclr_0 FROM ContainsJsons c0_",
            'with vars argument' => "SELECT jsonb_path_query_first(c0_.object1, '$.items[*].id', '{\"strict\": false}') AS sclr_0 FROM ContainsJsons c0_",
            'with vars and silent arguments' => "SELECT jsonb_path_query_first(c0_.object1, '$.items[*].id', '{\"strict\": false}', 'true') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts first item with condition' => \sprintf("SELECT JSONB_PATH_QUERY_FIRST(e.object1, '$.items[*] ? (@.price > 100)') FROM %s e", ContainsJsons::class),
            'extracts first item from path' => \sprintf("SELECT JSONB_PATH_QUERY_FIRST(e.object1, '$.items[*].id') FROM %s e", ContainsJsons::class),
            'with vars argument' => \sprintf("SELECT JSONB_PATH_QUERY_FIRST(e.object1, '$.items[*].id', '{\"strict\": false}') FROM %s e", ContainsJsons::class),
            'with vars and silent arguments' => \sprintf("SELECT JSONB_PATH_QUERY_FIRST(e.object1, '$.items[*].id', '{\"strict\": false}', 'true') FROM %s e", ContainsJsons::class),
        ];
    }

    public function test_invalid_boolean_throws_exception(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for jsonb_path_query_first. Must be "true" or "false".');

        $dql = \sprintf("SELECT JSONB_PATH_QUERY_FIRST(e.object1, '$.items[*].id', '{\"strict\": false}', 'invalid') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_path_query_first() requires at least 2 arguments');

        $dql = \sprintf('SELECT JSONB_PATH_QUERY_FIRST(e.object1) FROM %s e', ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_path_query_first() requires between 2 and 4 arguments');

        $dql = \sprintf("SELECT JSONB_PATH_QUERY_FIRST(e.object1, '$.items[*].id', '{\"strict\": false}', 'true', 'extra_arg') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
