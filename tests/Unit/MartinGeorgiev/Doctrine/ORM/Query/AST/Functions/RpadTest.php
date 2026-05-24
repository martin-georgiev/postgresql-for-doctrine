<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rpad;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RpadTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Rpad('RPAD');
    }

    protected function getStringFunctions(): array
    {
        return [
            'RPAD' => Rpad::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'pads a text field on the right to a given length' => 'SELECT rpad(c0_.text1, 10) AS sclr_0 FROM ContainsTexts c0_',
            'pads a text field on the right with a fill string' => "SELECT rpad(c0_.text1, 10, '0') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'pads a text field on the right to a given length' => \sprintf('SELECT RPAD(e.text1, 10) FROM %s e', ContainsTexts::class),
            'pads a text field on the right with a fill string' => \sprintf("SELECT RPAD(e.text1, 10, '0') FROM %s e", ContainsTexts::class),
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
                \sprintf('SELECT RPAD(e.text1) FROM %s e', ContainsTexts::class),
                'rpad() requires at least 2 arguments',
            ],
            'too many arguments' => [
                \sprintf("SELECT RPAD(e.text1, 10, '0', 'extra') FROM %s e", ContainsTexts::class),
                'rpad() requires between 2 and 3 arguments',
            ],
        ];
    }
}
