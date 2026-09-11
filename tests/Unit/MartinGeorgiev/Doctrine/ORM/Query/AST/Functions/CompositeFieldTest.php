<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsComposites;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CompositeField;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidFieldNameException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class CompositeFieldTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COMPOSITE_FIELD' => CompositeField::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'accesses field from composite type' => 'SELECT (c0_.item)."name" AS sclr_0 FROM ContainsComposites c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'accesses field from composite type' => \sprintf("SELECT COMPOSITE_FIELD(e.item, 'name') FROM %s e", ContainsComposites::class),
        ];
    }

    #[Test]
    public function throws_exception_for_non_string_field_name(): void
    {
        $this->expectException(QueryException::class);

        $dql = \sprintf('SELECT COMPOSITE_FIELD(e.item, 123) FROM %s e', ContainsComposites::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[Test]
    public function throws_exception_for_empty_field_name(): void
    {
        $this->expectException(InvalidFieldNameException::class);

        $dql = \sprintf("SELECT COMPOSITE_FIELD(e.item, '') FROM %s e", ContainsComposites::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }

    #[DataProvider('provideInjectionPayloads')]
    #[Test]
    public function escapes_field_name_into_a_single_quoted_identifier(string $fieldName, string $expectedSql): void
    {
        $dql = \sprintf("SELECT COMPOSITE_FIELD(e.item, '%s') FROM %s e", $fieldName, ContainsComposites::class);
        $this->assertSqlFromDql($expectedSql, $dql);
    }

    /**
     * @return array<string, array{fieldName: string, expectedSql: string}>
     */
    public static function provideInjectionPayloads(): array
    {
        return [
            'double quote breaking out of the identifier' => [
                'fieldName' => 'a", (SELECT version()) AS "x',
                'expectedSql' => 'SELECT (c0_.item)."a"", (SELECT version()) AS ""x" AS sclr_0 FROM ContainsComposites c0_',
            ],
            'trailing double quote' => [
                'fieldName' => 'name"',
                'expectedSql' => 'SELECT (c0_.item)."name""" AS sclr_0 FROM ContainsComposites c0_',
            ],
        ];
    }
}
