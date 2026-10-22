<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class RandomTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANDOM' => Random::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'generates random number' => 'SELECT RANDOM() AS sclr_0 FROM ContainsDecimals c0_',
            'uses random in arithmetic expression' => 'SELECT RANDOM() + c0_.decimal1 AS sclr_0 FROM ContainsDecimals c0_',
            'generates random number between literal bounds' => 'SELECT RANDOM(1, 10) AS sclr_0 FROM ContainsDecimals c0_',
            'generates random number between negative bounds' => 'SELECT RANDOM(-0.499, 0.499) AS sclr_0 FROM ContainsDecimals c0_',
            'generates random number between entity fields' => 'SELECT RANDOM(c0_.decimal1, c0_.decimal2) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'generates random number' => \sprintf('SELECT RANDOM() FROM %s e', ContainsDecimals::class),
            'uses random in arithmetic expression' => \sprintf('SELECT RANDOM() + e.decimal1 FROM %s e', ContainsDecimals::class),
            'generates random number between literal bounds' => \sprintf('SELECT RANDOM(1, 10) FROM %s e', ContainsDecimals::class),
            'generates random number between negative bounds' => \sprintf('SELECT RANDOM(-0.499, 0.499) FROM %s e', ContainsDecimals::class),
            'generates random number between entity fields' => \sprintf('SELECT RANDOM(e.decimal1, e.decimal2) FROM %s e', ContainsDecimals::class),
        ];
    }

    #[DataProvider('provideInvalidArgumentCountCases')]
    #[Test]
    public function throws_exception_for_invalid_argument_count(string $dql): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);

        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidArgumentCountCases(): array
    {
        return [
            'one argument' => [\sprintf('SELECT RANDOM(1) FROM %s e', ContainsDecimals::class)],
            'three arguments' => [\sprintf('SELECT RANDOM(1, 2, 3) FROM %s e', ContainsDecimals::class)],
        ];
    }
}
