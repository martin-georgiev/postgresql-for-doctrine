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
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'builds JSON object with field value' => \sprintf("SELECT JSON_BUILD_OBJECT('key1', e.object1) FROM %s e", ContainsJsons::class),
            'builds JSON object with function result and literal' => \sprintf("SELECT JSON_BUILD_OBJECT('key1', UPPER('value1'), 'key2', 'value2') FROM %s e", ContainsJsons::class),
            'builds JSON object with multiple field values' => \sprintf("SELECT JSON_BUILD_OBJECT('key1', e.object1, 'key2', e.object2) FROM %s e", ContainsJsons::class),
        ];
    }

    #[Test]
    public function throws_exception_for_odd_number_of_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT JSON_BUILD_OBJECT('key1', e.value1, 'key2') FROM %s e", ContainsJsons::class);
        $this->assertSqlFromDql('', $dql);
    }
}
