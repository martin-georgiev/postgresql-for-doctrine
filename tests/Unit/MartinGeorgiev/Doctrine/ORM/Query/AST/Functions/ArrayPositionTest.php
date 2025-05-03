<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

class ArrayPositionTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new ArrayPosition('ARRAY_POSITION');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_POSITION' => ArrayPosition::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'finds string element' => "SELECT array_position(c0_.array1, 'new-value') AS sclr_0 FROM ContainsArrays c0_",
            'finds numeric element' => 'SELECT array_position(c0_.array1, 42) AS sclr_0 FROM ContainsArrays c0_',
            'finds element using parameter' => 'SELECT array_position(c0_.array1, ?) AS sclr_0 FROM ContainsArrays c0_',
            'with start position' => "SELECT array_position(c0_.array1, 'new-value', 2) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'finds string element' => \sprintf("SELECT ARRAY_POSITION(e.array1, 'new-value') FROM %s e", ContainsArrays::class),
            'finds numeric element' => \sprintf('SELECT ARRAY_POSITION(e.array1, 42) FROM %s e', ContainsArrays::class),
            'finds element using parameter' => \sprintf('SELECT ARRAY_POSITION(e.array1, :dql_parameter) FROM %s e', ContainsArrays::class),
            'with start position' => \sprintf("SELECT ARRAY_POSITION(e.array1, 'new-value', 2) FROM %s e", ContainsArrays::class),
        ];
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('array_position() requires at least 2 arguments');

        $dql = \sprintf('SELECT ARRAY_POSITION(e.array1) FROM %s e', ContainsArrays::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('array_position() requires between 2 and 3 arguments');

        $dql = \sprintf("SELECT ARRAY_POSITION(e.array1, 0, 1, 'extra_arg') FROM %s e", ContainsArrays::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
