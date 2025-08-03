<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject;
use PHPUnit\Framework\Attributes\Test;

class JsonBuildObjectTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new JsonBuildObject('JSON_BUILD_OBJECT');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSON_BUILD_OBJECT' => JsonBuildObject::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'builds JSON object with field value' => "SELECT json_build_object('key1', c0_.object1) AS sclr_0 FROM ContainsJsons c0_",
            'builds JSON object with function result and literal' => "SELECT json_build_object('key1', UPPER('value1'), 'key2', 'value2') AS sclr_0 FROM ContainsJsons c0_",
            'builds JSON object with multiple field values' => "SELECT json_build_object('key1', c0_.object1, 'key2', c0_.object2) AS sclr_0 FROM ContainsJsons c0_",
            'builds JSON object with many key-value pairs' => "SELECT json_build_object('k1', 'v1', 'k2', 'v2', 'k3', 'v3', 'k4', 'v4') AS sclr_0 FROM ContainsJsons c0_",
            'builds JSON object with numeric and boolean values' => "SELECT json_build_object('numeric_key', '123', 'boolean_key', 'true') AS sclr_0 FROM ContainsJsons c0_",
            'builds JSON object with mixed field and literal values' => "SELECT json_build_object('field_key', c0_.object1, 'literal_key', 'literal_value') AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'builds JSON object with field value' => \sprintf("SELECT JSON_BUILD_OBJECT('key1', e.object1) FROM %s e", ContainsJsons::class),
            'builds JSON object with function result and literal' => \sprintf("SELECT JSON_BUILD_OBJECT('key1', UPPER('value1'), 'key2', 'value2') FROM %s e", ContainsJsons::class),
            'builds JSON object with multiple field values' => \sprintf("SELECT JSON_BUILD_OBJECT('key1', e.object1, 'key2', e.object2) FROM %s e", ContainsJsons::class),
            'builds JSON object with many key-value pairs' => \sprintf("SELECT JSON_BUILD_OBJECT('k1', 'v1', 'k2', 'v2', 'k3', 'v3', 'k4', 'v4') FROM %s e", ContainsJsons::class),
            'builds JSON object with numeric and boolean values' => \sprintf("SELECT JSON_BUILD_OBJECT('numeric_key', '123', 'boolean_key', 'true') FROM %s e", ContainsJsons::class),
            'builds JSON object with mixed field and literal values' => \sprintf("SELECT JSON_BUILD_OBJECT('field_key', e.object1, 'literal_key', 'literal_value') FROM %s e", ContainsJsons::class),
        ];
    }

    #[Test]
    public function throws_exception_for_odd_number_of_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('json_build_object() requires an even number of arguments');

        $dql = \sprintf("SELECT JSON_BUILD_OBJECT('key1', e.object1, 'key2') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_single_argument(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('json_build_object() requires at least 2 arguments');

        $dql = \sprintf("SELECT JSON_BUILD_OBJECT('key1') FROM %s e", ContainsJsons::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
