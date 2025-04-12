<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateAdd;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidTimezoneException;

class DateAddTest extends TestCase
{
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
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'adds 1 day with timezone' => \sprintf("SELECT DATE_ADD(e.datetimetz1, '1 day', 'Europe/Sofia') FROM %s e", ContainsDates::class),
            'adds 2 hours with timezone' => \sprintf("SELECT DATE_ADD(e.datetimetz1, '2 hours', 'UTC') FROM %s e", ContainsDates::class),
            'adds 3 days without timezone' => \sprintf("SELECT DATE_ADD(e.datetimetz1, '3 days') FROM %s e", ContainsDates::class),
        ];
    }

    public function test_invalid_timezone_throws_exception(): void
    {
        $this->expectException(InvalidTimezoneException::class);
        $this->expectExceptionMessage('Invalid timezone "Invalid/Timezone" provided for DATE_ADD');

        $dql = \sprintf("SELECT DATE_ADD(e.datetimetz1, '1 day', 'Invalid/Timezone') FROM %s e", ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
