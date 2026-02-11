<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch\Levenshtein;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunctionTestCase;

class LevenshteinTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Levenshtein('LEVENSHTEIN');
    }

    protected function getStringFunctions(): array
    {
        return [
            'LEVENSHTEIN' => Levenshtein::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic two strings' => 'SELECT levenshtein(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
            'with custom costs' => 'SELECT levenshtein(c0_.text1, c0_.text2, 1, 2, 3) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic two strings' => \sprintf('SELECT LEVENSHTEIN(e.text1, e.text2) FROM %s e', ContainsTexts::class),
            'with custom costs' => \sprintf('SELECT LEVENSHTEIN(e.text1, e.text2, 1, 2, 3) FROM %s e', ContainsTexts::class),
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
                \sprintf('SELECT LEVENSHTEIN(e.text1) FROM %s e', ContainsTexts::class),
                'levenshtein() requires at least 2 arguments',
            ],
            'too many arguments' => [
                \sprintf('SELECT LEVENSHTEIN(e.text1, e.text2, 1, 2, 3, 4) FROM %s e', ContainsTexts::class),
                'levenshtein() requires between 2 and 5 arguments',
            ],
            'invalid argument count (3 args)' => [
                \sprintf('SELECT LEVENSHTEIN(e.text1, e.text2, 1) FROM %s e', ContainsTexts::class),
                'levenshtein() cannot be called with 3 arguments',
            ],
            'invalid argument count (4 args)' => [
                \sprintf('SELECT LEVENSHTEIN(e.text1, e.text2, 1, 2) FROM %s e', ContainsTexts::class),
                'levenshtein() cannot be called with 4 arguments',
            ],
        ];
    }
}
