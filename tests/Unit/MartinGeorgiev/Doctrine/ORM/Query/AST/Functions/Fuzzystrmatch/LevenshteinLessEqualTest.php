<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\LevenshteinLessEqual;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionTestCase;

class LevenshteinLessEqualTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new LevenshteinLessEqual('LEVENSHTEIN_LESS_EQUAL');
    }

    protected function getStringFunctions(): array
    {
        return [
            'LEVENSHTEIN_LESS_EQUAL' => LevenshteinLessEqual::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with max distance' => 'SELECT levenshtein_less_equal(c0_.text1, c0_.text2, 2) AS sclr_0 FROM ContainsTexts c0_',
            'with custom costs' => 'SELECT levenshtein_less_equal(c0_.text1, c0_.text2, 1, 2, 3, 5) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with max distance' => \sprintf('SELECT LEVENSHTEIN_LESS_EQUAL(e.text1, e.text2, 2) FROM %s e', ContainsTexts::class),
            'with custom costs' => \sprintf('SELECT LEVENSHTEIN_LESS_EQUAL(e.text1, e.text2, 1, 2, 3, 5) FROM %s e', ContainsTexts::class),
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
                \sprintf('SELECT LEVENSHTEIN_LESS_EQUAL(e.text1, e.text2) FROM %s e', ContainsTexts::class),
                'levenshtein_less_equal() requires at least 3 arguments',
            ],
            'too many arguments' => [
                \sprintf('SELECT LEVENSHTEIN_LESS_EQUAL(e.text1, e.text2, 1, 2, 3, 4, 5) FROM %s e', ContainsTexts::class),
                'levenshtein_less_equal() requires between 3 and 6 arguments',
            ],
            'invalid argument count (4 args)' => [
                \sprintf('SELECT LEVENSHTEIN_LESS_EQUAL(e.text1, e.text2, 1, 2) FROM %s e', ContainsTexts::class),
                'levenshtein_less_equal() cannot be called with 4 arguments',
            ],
            'invalid argument count (5 args)' => [
                \sprintf('SELECT LEVENSHTEIN_LESS_EQUAL(e.text1, e.text2, 1, 2, 3) FROM %s e', ContainsTexts::class),
                'levenshtein_less_equal() cannot be called with 5 arguments',
            ],
        ];
    }
}
