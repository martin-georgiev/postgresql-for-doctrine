<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject;
use PHPUnit\Framework\Attributes\Test;

class JsonbBuildObjectTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new JsonbBuildObject('JSONB_BUILD_OBJECT');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSONB_BUILD_OBJECT' => JsonbBuildObject::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'builds JSONB object with field value' => "SELECT jsonb_build_object('key1', c0_.jsonbObject1) AS sclr_0 FROM ContainsJsons c0_",
            'builds JSONB object with function result and literal' => "SELECT jsonb_build_object('key1', UPPER('value1'), 'key2', 'value2') AS sclr_0 FROM ContainsJsons c0_",
            'builds JSONB object with multiple field values' => "SELECT jsonb_build_object('key1', c0_.jsonbObject1, 'key2', c0_.jsonbObject2) AS sclr_0 FROM ContainsJsons c0_",
            'builds JSONB object with many key-value pairs' => "SELECT jsonb_build_object('k1', 'v1', 'k2', 'v2', 'k3', 'v3', 'k4', 'v4') AS sclr_0 FROM ContainsJsons c0_",
            'builds JSONB object with numeric and boolean values' => "SELECT jsonb_build_object('numeric_key', '123', 'boolean_key', 'true') AS sclr_0 FROM ContainsJsons c0_",
            'builds JSONB object with mixed field and literal values' => "SELECT jsonb_build_object('field_key', c0_.jsonbObject1, 'literal_key', 'literal_value') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'builds JSONB object with field value' => \sprintf("SELECT JSONB_BUILD_OBJECT('key1', e.jsonbObject1) FROM %s e", ContainsJsons::class),
            'builds JSONB object with function result and literal' => \sprintf("SELECT JSONB_BUILD_OBJECT('key1', UPPER('value1'), 'key2', 'value2') FROM %s e", ContainsJsons::class),
            'builds JSONB object with multiple field values' => \sprintf("SELECT JSONB_BUILD_OBJECT('key1', e.jsonbObject1, 'key2', e.jsonbObject2) FROM %s e", ContainsJsons::class),
            'builds JSONB object with many key-value pairs' => \sprintf("SELECT JSONB_BUILD_OBJECT('k1', 'v1', 'k2', 'v2', 'k3', 'v3', 'k4', 'v4') FROM %s e", ContainsJsons::class),
            'builds JSONB object with numeric and boolean values' => \sprintf("SELECT JSONB_BUILD_OBJECT('numeric_key', '123', 'boolean_key', 'true') FROM %s e", ContainsJsons::class),
            'builds JSONB object with mixed field and literal values' => \sprintf("SELECT JSONB_BUILD_OBJECT('field_key', e.jsonbObject1, 'literal_key', 'literal_value') FROM %s e", ContainsJsons::class),
        ];
    }

    #[Test]
    public function throws_exception_for_odd_number_of_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_build_object() requires an even number of arguments');

        $dql = \sprintf("SELECT JSONB_BUILD_OBJECT('key1', e.jsonbObject1, 'key2') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_single_argument(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('jsonb_build_object() requires at least 2 arguments');

        $dql = \sprintf("SELECT JSONB_BUILD_OBJECT('key1') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
