<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArraySort;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ArraySortTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_SORT' => ArraySort::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'sorts text array' => 'SELECT array_sort(c0_.textArray) AS sclr_0 FROM ContainsArrays c0_',
            'sorts integer array' => 'SELECT array_sort(c0_.integerArray) AS sclr_0 FROM ContainsArrays c0_',
            'sorts in descending order' => "SELECT array_sort(c0_.integerArray, 'true') AS sclr_0 FROM ContainsArrays c0_",
            'sorts in descending order with nulls first' => "SELECT array_sort(c0_.integerArray, 'true', 'true') AS sclr_0 FROM ContainsArrays c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'sorts text array' => \sprintf('SELECT ARRAY_SORT(e.textArray) FROM %s e', ContainsArrays::class),
            'sorts integer array' => \sprintf('SELECT ARRAY_SORT(e.integerArray) FROM %s e', ContainsArrays::class),
            'sorts in descending order' => \sprintf("SELECT ARRAY_SORT(e.integerArray, 'true') FROM %s e", ContainsArrays::class),
            'sorts in descending order with nulls first' => \sprintf("SELECT ARRAY_SORT(e.integerArray, 'true', 'true') FROM %s e", ContainsArrays::class),
        ];
    }

    #[DataProvider('provideInvalidBooleanArguments')]
    #[Test]
    public function throws_exception_for_invalid_boolean(string $dql): void
    {
        $this->expectException(InvalidBooleanException::class);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidBooleanArguments(): array
    {
        return [
            'invalid descending' => [\sprintf("SELECT ARRAY_SORT(e.integerArray, 'invalid') FROM %s e", ContainsArrays::class)],
            'invalid nulls first' => [\sprintf("SELECT ARRAY_SORT(e.integerArray, 'true', 'invalid') FROM %s e", ContainsArrays::class)],
            'field as descending' => [\sprintf('SELECT ARRAY_SORT(e.integerArray, e.textArray) FROM %s e', ContainsArrays::class)],
        ];
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $dql = \sprintf("SELECT ARRAY_SORT(e.integerArray, 'true', 'true', 'true') FROM %s e", ContainsArrays::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
