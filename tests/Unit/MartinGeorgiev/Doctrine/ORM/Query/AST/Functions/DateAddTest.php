<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidTimezoneException;

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
            'adds 1 day with timezone' => "SELECT date_add(c0_.datetimetz1, '1 day', 'Europe/Sofia') AS sclr_0 FROM ContainsDates c0_",
            'adds 2 hours with timezone' => "SELECT date_add(c0_.datetimetz1, '2 hours', 'UTC') AS sclr_0 FROM ContainsDates c0_",
            'adds 3 days without timezone' => "SELECT date_add(c0_.datetimetz1, '3 days') AS sclr_0 FROM ContainsDates c0_",
            'adds with WHERE clause' => "SELECT c0_.datetimetz1 AS datetimetz1_0 FROM ContainsDates c0_ WHERE date_add(c0_.datetimetz1, '1 day') = '2023-01-02 00:00:00'",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'adds 1 day with timezone' => \sprintf("SELECT DATE_ADD(e.datetimetz1, '1 day', 'Europe/Sofia') FROM %s e", ContainsDates::class),
            'adds 2 hours with timezone' => \sprintf("SELECT DATE_ADD(e.datetimetz1, '2 hours', 'UTC') FROM %s e", ContainsDates::class),
            'adds 3 days without timezone' => \sprintf("SELECT DATE_ADD(e.datetimetz1, '3 days') FROM %s e", ContainsDates::class),
            'adds with WHERE clause' => \sprintf("SELECT e.datetimetz1 FROM %s e WHERE DATE_ADD(e.datetimetz1, '1 day') = '2023-01-02 00:00:00'", ContainsDates::class),
        ];
    }

    public function test_invalid_timezone_throws_exception(): void
    {
        $this->expectException(InvalidTimezoneException::class);
        $this->expectExceptionMessage('Invalid timezone "Invalid/Timezone" provided for date_add');

        $dql = \sprintf("SELECT DATE_ADD(e.datetimetz1, '1 day', 'Invalid/Timezone') FROM %s e", ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_few_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('date_add() requires at least 2 arguments');

        $dql = \sprintf('SELECT DATE_ADD(e.datetimetz1) FROM %s e', ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    public function test_too_many_arguments_throws_exception(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('date_add() requires between 2 and 3 arguments');

        $dql = \sprintf("SELECT DATE_ADD(e.datetimetz1, '1 day', 'Europe/Sofia', 'extra_arg') FROM %s e", ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
