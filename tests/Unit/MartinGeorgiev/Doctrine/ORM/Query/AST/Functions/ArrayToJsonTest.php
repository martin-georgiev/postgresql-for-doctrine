<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use PHPUnit\Framework\Attributes\Test;

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
            'converts array to json' => 'SELECT array_to_json(c0_.textArray) AS sclr_0 FROM ContainsArrays c0_',
            'converts array to json with pretty print' => "SELECT array_to_json(c0_.textArray, 'true') AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts array to json' => \sprintf('SELECT ARRAY_TO_JSON(e.textArray) FROM %s e', ContainsArrays::class),
            'converts array to json with pretty print' => \sprintf("SELECT ARRAY_TO_JSON(e.textArray, 'true') FROM %s e", ContainsArrays::class),
        ];
    }

    #[Test]
    public function throws_exception_for_invalid_boolean_value(): void
    {
        $this->expectException(InvalidBooleanException::class);
        $this->expectExceptionMessage('Invalid boolean value "invalid" provided for array_to_json. Must be "true" or "false".');

        $dql = \sprintf("SELECT ARRAY_TO_JSON(e.textArray, 'invalid') FROM %s e", ContainsArrays::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('array_to_json() requires between 1 and 2 arguments');

        $dql = \sprintf("SELECT ARRAY_TO_JSON(e.textArray, 'true', 'extra_arg') FROM %s e", ContainsArrays::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
