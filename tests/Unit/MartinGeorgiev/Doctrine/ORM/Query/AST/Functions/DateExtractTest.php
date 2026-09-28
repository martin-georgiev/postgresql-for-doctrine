<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidFieldNameException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class DateExtractTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_EXTRACT' => DateExtract::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'extracts day from date' => "SELECT EXTRACT('DAY' FROM c0_.date1) AS sclr_0 FROM ContainsDates c0_",
            'extracts month from date' => "SELECT EXTRACT('MONTH' FROM c0_.date1) AS sclr_0 FROM ContainsDates c0_",
            'extracts year from date' => "SELECT EXTRACT('YEAR' FROM c0_.date1) AS sclr_0 FROM ContainsDates c0_",
            'filters by extracted day' => "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE EXTRACT('DAY' FROM c0_.date1) = 7",
            'filters by extracted month' => "SELECT c0_.date1 AS date1_0 FROM ContainsDates c0_ WHERE EXTRACT('MONTH' FROM c0_.date1) = 12",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'extracts day from date' => \sprintf("SELECT DATE_EXTRACT('DAY', e.date1) FROM %s e", ContainsDates::class),
            'extracts month from date' => \sprintf("SELECT DATE_EXTRACT('MONTH', e.date1) FROM %s e", ContainsDates::class),
            'extracts year from date' => \sprintf("SELECT DATE_EXTRACT('YEAR', e.date1) FROM %s e", ContainsDates::class),
            'filters by extracted day' => \sprintf("SELECT e.date1 FROM %s e WHERE DATE_EXTRACT('DAY', e.date1) = 7", ContainsDates::class),
            'filters by extracted month' => \sprintf("SELECT e.date1 FROM %s e WHERE DATE_EXTRACT('MONTH', e.date1) = 12", ContainsDates::class),
        ];
    }

    /**
     * PostgreSQL's EXTRACT(field FROM source) takes the field as a keyword or a string literal, so anything else fails there.
     */
    #[DataProvider('provideFieldsThatAreNotStringLiterals')]
    #[Test]
    public function throws_exception_for_a_field_that_is_not_a_string_literal(string $field): void
    {
        $this->expectException(InvalidFieldNameException::class);
        $this->expectExceptionMessage('DATE_EXTRACT() requires a string literal as the field name argument');

        $dql = \sprintf('SELECT DATE_EXTRACT(%s, e.date1) FROM %s e', $field, ContainsDates::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function provideFieldsThatAreNotStringLiterals(): array
    {
        return [
            'parameter' => [':field'],
            'function call' => ["LOWER('DAY')"],
            'entity field' => ['e.date1'],
        ];
    }
}
