<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestPatternFallbackFunction;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestPatternRewindFunction;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestShorterPatternFallbackFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BaseVariadicFunctionPatternFallbackTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TEST_PATTERN_FALLBACK' => TestPatternFallbackFunction::class,
            'TEST_PATTERN_REWIND' => TestPatternRewindFunction::class,
            'TEST_SHORTER_PATTERN_FALLBACK' => TestShorterPatternFallbackFunction::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'falls back to the next pattern once the first pattern fails on the second argument' => 'SELECT test_pattern_fallback(c0_.text1, 1) AS sclr_0 FROM ContainsTexts c0_',
            'takes a single argument through the shorter pattern' => 'SELECT test_shorter_pattern_fallback(1) AS sclr_0 FROM ContainsTexts c0_',
            'takes two arguments through the longer pattern' => "SELECT test_shorter_pattern_fallback(c0_.text1, 'x') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'falls back to the next pattern once the first pattern fails on the second argument' => \sprintf('SELECT TEST_PATTERN_FALLBACK(e.text1, TRUE) FROM %s e', ContainsTexts::class),
            'takes a single argument through the shorter pattern' => \sprintf('SELECT TEST_SHORTER_PATTERN_FALLBACK(1) FROM %s e', ContainsTexts::class),
            'takes two arguments through the longer pattern' => \sprintf("SELECT TEST_SHORTER_PATTERN_FALLBACK(e.text1, 'x') FROM %s e", ContainsTexts::class),
        ];
    }

    #[DataProvider('provideUnparsableArgumentLists')]
    #[Test]
    public function throws_parser_exception_when_no_pattern_can_parse_the_argument_list(string $dql): void
    {
        $this->expectException(ParserException::class);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{dql: string}>
     */
    public static function provideUnparsableArgumentLists(): array
    {
        return [
            'no pattern accepts the arguments' => ['dql' => \sprintf('SELECT TEST_PATTERN_FALLBACK(NULL, NULL) FROM %s e', ContainsTexts::class)],
            'the next pattern rejects the argument list from its start' => ['dql' => \sprintf('SELECT TEST_PATTERN_REWIND(e.text1, 1) FROM %s e', ContainsTexts::class)],
            'only a pattern shorter than the argument list accepts it' => ['dql' => \sprintf("SELECT TEST_SHORTER_PATTERN_FALLBACK('a', 1) FROM %s e", ContainsTexts::class)],
        ];
    }
}
