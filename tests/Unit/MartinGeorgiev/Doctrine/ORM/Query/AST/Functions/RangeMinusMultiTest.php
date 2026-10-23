<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeMinusMulti;

final class RangeMinusMultiTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANGE_MINUS_MULTI' => RangeMinusMulti::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'subtracts a literal range from an entity field' => "SELECT range_minus_multi(c0_.int4Range, '[3,4)') AS sclr_0 FROM ContainsRanges c0_",
            'subtracts one entity field from another' => 'SELECT range_minus_multi(c0_.dateRange, c0_.dateRange) AS sclr_0 FROM ContainsRanges c0_',
            'subtracts a parameter from an entity field' => 'SELECT range_minus_multi(c0_.tsRange, ?) AS sclr_0 FROM ContainsRanges c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'subtracts a literal range from an entity field' => \sprintf("SELECT RANGE_MINUS_MULTI(e.int4Range, '[3,4)') FROM %s e", ContainsRanges::class),
            'subtracts one entity field from another' => \sprintf('SELECT RANGE_MINUS_MULTI(e.dateRange, e.dateRange) FROM %s e', ContainsRanges::class),
            'subtracts a parameter from an entity field' => \sprintf('SELECT RANGE_MINUS_MULTI(e.tsRange, :range) FROM %s e', ContainsRanges::class),
        ];
    }
}
