<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsComposites;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CompositeField;
use PHPUnit\Framework\Attributes\Test;

class CompositeFieldTest extends TestCase
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
}
