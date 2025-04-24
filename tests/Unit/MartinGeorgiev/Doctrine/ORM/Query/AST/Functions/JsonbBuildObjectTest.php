<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject;

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
            "SELECT jsonb_build_object('key1', c0_.object1) AS sclr_0 FROM ContainsJsons c0_",
            "SELECT jsonb_build_object('key1', UPPER('value1'), 'key2', 'value2') AS sclr_0 FROM ContainsJsons c0_",
            "SELECT jsonb_build_object('key1', c0_.object1, 'key2', c0_.object2) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT JSONB_BUILD_OBJECT('key1', e.object1) FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSONB_BUILD_OBJECT('key1', UPPER('value1'), 'key2', 'value2') FROM %s e", ContainsJsons::class),
            \sprintf("SELECT JSONB_BUILD_OBJECT('key1', e.object1, 'key2', e.object2) FROM %s e", ContainsJsons::class),
        ];
    }

    /**
     * @test
     */
    public function throws_exception_when_odd_number_of_arguments_given(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT JSONB_BUILD_OBJECT('key1', e.object1, 'key2') FROM %s e", ContainsJsons::class);
        $this->assertSqlFromDql('', $dql);
    }
}
