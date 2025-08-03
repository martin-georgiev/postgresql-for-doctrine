<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPosition;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

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
            'finds string element' => "SELECT array_position(c0_.textArray, 'new-value') AS sclr_0 FROM ContainsArrays c0_",
            'finds numeric element' => 'SELECT array_position(c0_.integerArray, 42) AS sclr_0 FROM ContainsArrays c0_',
            'finds element using parameter' => 'SELECT array_position(c0_.textArray, ?) AS sclr_0 FROM ContainsArrays c0_',
            'with start position' => "SELECT array_position(c0_.textArray, 'new-value', 2) AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'finds string element' => \sprintf("SELECT ARRAY_POSITION(e.textArray, 'new-value') FROM %s e", ContainsArrays::class),
            'finds numeric element' => \sprintf('SELECT ARRAY_POSITION(e.integerArray, 42) FROM %s e', ContainsArrays::class),
            'finds element using parameter' => \sprintf('SELECT ARRAY_POSITION(e.textArray, :dql_parameter) FROM %s e', ContainsArrays::class),
            'with start position' => \sprintf("SELECT ARRAY_POSITION(e.textArray, 'new-value', 2) FROM %s e", ContainsArrays::class),
        ];
    }

    #[DataProvider('provideInvalidArgumentCountCases')]
    #[Test]
    public function throws_exception_for_invalid_argument_count(string $dql, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function provideInvalidArgumentCountCases(): array
    {
        return [
            'too few arguments' => [
                \sprintf('SELECT ARRAY_POSITION(e.textArray) FROM %s e', ContainsArrays::class),
                'array_position() requires at least 2 arguments',
            ],
            'too many arguments' => [
                \sprintf("SELECT ARRAY_POSITION(e.textArray, 0, 1, 'extra_arg') FROM %s e", ContainsArrays::class),
                'array_position() requires between 2 and 3 arguments',
            ],
        ];
    }
}
