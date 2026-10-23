<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MultirangeMinusMulti;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeAgg;

final class MultirangeMinusMultiTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MULTIRANGE_MINUS_MULTI' => MultirangeMinusMulti::class,
            'RANGE_AGG' => RangeAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'subtracts a literal multirange from an aggregated entity field' => "SELECT multirange_minus_multi(range_agg(c0_.int4Range), '{[3,4)}') AS sclr_0 FROM ContainsRanges c0_",
            'subtracts a parameter from an aggregated entity field' => 'SELECT multirange_minus_multi(range_agg(c0_.dateRange), ?) AS sclr_0 FROM ContainsRanges c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'subtracts a literal multirange from an aggregated entity field' => \sprintf("SELECT MULTIRANGE_MINUS_MULTI(RANGE_AGG(e.int4Range), '{[3,4)}') FROM %s e", ContainsRanges::class),
            'subtracts a parameter from an aggregated entity field' => \sprintf('SELECT MULTIRANGE_MINUS_MULTI(RANGE_AGG(e.dateRange), :multirange) FROM %s e', ContainsRanges::class),
        ];
    }
}
