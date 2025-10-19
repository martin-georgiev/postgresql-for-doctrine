<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidTimezoneException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

class DateAddTest extends BaseVariadicFunctionTestCase
{
    protected function createFixture(): BaseVariadicFunction
    {
        return new DateAdd('DATE_ADD');
    }

    protected function getStringFunctions(): array
    {
        return [
            'DATE_ADD' => DateAdd::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'with timezone (3 arguments)' => "SELECT date_add(c0_.datetimetz1, '1 day', 'Europe/Sofia') AS sclr_0 FROM ContainsDates c0_",
            'without timezone (2 arguments)' => "SELECT date_add(c0_.datetimetz1, '3 days') AS sclr_0 FROM ContainsDates c0_",
            'used in WHERE clause' => "SELECT c0_.datetimetz1 AS datetimetz1_0 FROM ContainsDates c0_ WHERE date_add(c0_.datetimetz1, '1 day') = '2023-01-02 00:00:00'",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'with timezone (3 arguments)' => \sprintf("SELECT DATE_ADD(e.datetimetz1, '1 day', 'Europe/Sofia') FROM %s e", ContainsDates::class),
            'without timezone (2 arguments)' => \sprintf("SELECT DATE_ADD(e.datetimetz1, '3 days') FROM %s e", ContainsDates::class),
            'used in WHERE clause' => \sprintf("SELECT e.datetimetz1 FROM %s e WHERE DATE_ADD(e.datetimetz1, '1 day') = '2023-01-02 00:00:00'", ContainsDates::class),
        ];
    }

    #[Test]
    public function throws_exception_for_too_few_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('date_add() requires at least 2 arguments');

        $dql = \sprintf('SELECT DATE_ADD(e.datetimetz1) FROM %s e', ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_too_many_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('date_add() requires between 2 and 3 arguments');

        $dql = \sprintf("SELECT DATE_ADD(e.datetimetz1, '1 day', 'Europe/Sofia', 'extra_arg') FROM %s e", ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[DataProvider('provideInvalidTimezoneValues')]
    #[Test]
    public function throws_exception_for_invalid_timezone(string $invalidTimezone): void
    {
        $this->expectException(InvalidTimezoneException::class);
        $this->expectExceptionMessage(\sprintf('Invalid timezone "%s" provided for date_add. Must be a valid PHP timezone identifier.', $invalidTimezone));

        $dql = \sprintf("SELECT DATE_ADD(e.datetimetz1, '1 day', '%s') FROM %s e", $invalidTimezone, ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideInvalidTimezoneValues(): array
    {
        return [
            'empty string' => [''],
            'whitespace only' => ['   '],
            'numeric value' => ['123'],
            'invalid timezone' => ['Invalid/Timezone'],
        ];
    }
}
