<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use Fixtures\MartinGeorgiev\Doctrine\Function\TestPatternFallbackFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use PHPUnit\Framework\Attributes\Test;

final class BaseVariadicFunctionPatternFallbackTest extends TestCase
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
            'falls back to the next pattern once the first pattern fails on the second argument' => 'SELECT test_pattern_fallback(c0_.text1, 1) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'falls back to the next pattern once the first pattern fails on the second argument' => \sprintf('SELECT TEST_PATTERN_FALLBACK(e.text1, TRUE) FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_parser_exception_when_no_pattern_can_parse_the_argument_list(): void
    {
        $this->expectException(ParserException::class);

        $dql = \sprintf('SELECT TEST_PATTERN_FALLBACK(NULL, NULL) FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
