<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsComposites;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\CompositeField;

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
            'accesses field from composite type' => 'SELECT (c0_.item).name AS sclr_0 FROM ContainsComposites c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'accesses field from composite type' => \sprintf("SELECT COMPOSITE_FIELD(e.item, 'name') FROM %s e", ContainsComposites::class),
        ];
    }
}
