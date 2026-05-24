<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Rtrim;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class RtrimTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new Rtrim('RTRIM');
    }

    protected function getStringFunctions(): array
    {
        return [
            'RTRIM' => Rtrim::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'trims whitespace from the right of a text field' => 'SELECT rtrim(c0_.text1) AS sclr_0 FROM ContainsTexts c0_',
            'trims specified characters from the right of a text field' => "SELECT rtrim(c0_.text1, ' x') AS sclr_0 FROM ContainsTexts c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'trims whitespace from the right of a text field' => \sprintf('SELECT RTRIM(e.text1) FROM %s e', ContainsTexts::class),
            'trims specified characters from the right of a text field' => \sprintf("SELECT RTRIM(e.text1, ' x') FROM %s e", ContainsTexts::class),
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
            'too many arguments' => [
                \sprintf("SELECT RTRIM(e.text1, ' ', 'extra') FROM %s e", ContainsTexts::class),
                'rtrim() requires between 1 and 2 arguments',
            ],
        ];
    }
}
