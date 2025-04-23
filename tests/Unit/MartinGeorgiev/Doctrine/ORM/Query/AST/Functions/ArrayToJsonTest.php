<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;

class ArrayToJsonTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new ArrayToJson('ARRAY_TO_JSON');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_TO_JSON' => ArrayToJson::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts array to json' => 'SELECT array_to_json(c0_.array1) AS sclr_0 FROM ContainsArrays c0_',
            'converts array to json with pretty print' => "SELECT array_to_json(c0_.array1, 'true') AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts array to json' => \sprintf('SELECT ARRAY_TO_JSON(e.array1) FROM %s e', ContainsArrays::class),
            'converts array to json with pretty print' => \sprintf("SELECT ARRAY_TO_JSON(e.array1, 'true') FROM %s e", ContainsArrays::class),
        ];
    }

    public function test_invalid_boolean_throws_exception(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for array_to_json. Must be "true" or "false".');

        $dql = \sprintf("SELECT ARRAY_TO_JSON(e.array1, 'invalid') FROM %s e", ContainsArrays::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('array_to_json() requires between 1 and 2 arguments');

        $dql = \sprintf("SELECT ARRAY_TO_JSON(e.array1, 'true', 'extra_arg') FROM %s e", ContainsArrays::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
