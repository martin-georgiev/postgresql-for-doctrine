<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestPatternFallbackFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class BaseVariadicFunctionTruncatedArgumentListTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TEST_PATTERN_FALLBACK' => TestPatternFallbackFunction::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'complete argument list' => 'SELECT test_pattern_fallback(c0_.text1, c0_.text2) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'complete argument list' => \sprintf('SELECT TEST_PATTERN_FALLBACK(e.text1, e.text2) FROM %s e', ContainsTexts::class),
        ];
    }

    #[DataProvider('provideTruncatedArgumentLists')]
    #[Test]
    public function throws_exception_when_the_argument_list_is_truncated(string $dql, string $message): void
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessage($message);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{dql: string, message: string}>
     */
    public static function provideTruncatedArgumentLists(): array
    {
        return [
            'nothing follows the opening parenthesis' => ['dql' => 'SELECT TEST_PATTERN_FALLBACK(', 'message' => 'test_pattern_fallback() requires at least 2 arguments'],
            'nothing follows the first argument' => ['dql' => 'SELECT TEST_PATTERN_FALLBACK(e.text1', 'message' => 'Cannot parse the argument list of test_pattern_fallback(). Expected a comma or a closing parenthesis after an argument.'],
            'nothing follows the argument separator' => ['dql' => 'SELECT TEST_PATTERN_FALLBACK(e.text1,', 'message' => 'test_pattern_fallback() requires at least 2 arguments'],
        ];
    }
}
