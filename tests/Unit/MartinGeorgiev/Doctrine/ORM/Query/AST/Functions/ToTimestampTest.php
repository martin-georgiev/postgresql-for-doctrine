<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class ToTimestampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TIMESTAMP' => ToTimestamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts text to timestamp using format pattern' => "SELECT to_timestamp(c0_.text1, 'DD Mon YYYY') AS sclr_0 FROM ContainsTexts c0_",
            'converts a Unix epoch to timestamp' => 'SELECT to_timestamp(1700000000) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts text to timestamp using format pattern' => \sprintf("SELECT TO_TIMESTAMP(e.text1, 'DD Mon YYYY') FROM %s e", ContainsTexts::class),
            'converts a Unix epoch to timestamp' => \sprintf('SELECT TO_TIMESTAMP(1700000000) FROM %s e', ContainsTexts::class),
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
                \sprintf('SELECT TO_TIMESTAMP() FROM %s e', ContainsTexts::class),
                'to_timestamp() requires at least 1 argument',
            ],
            'too many arguments' => [
                \sprintf("SELECT TO_TIMESTAMP(e.text1, 'DD Mon YYYY', 'extra') FROM %s e", ContainsTexts::class),
                'to_timestamp() requires between 1 and 2 arguments',
            ],
        ];
    }

    #[Test]
    public function throws_exception_for_a_numeric_format_argument(): void
    {
        $this->expectException(ParserException::class);

        $dql = \sprintf("SELECT TO_TIMESTAMP('05 Dec 2000', 1) FROM %s e", ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
