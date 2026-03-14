<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeAgg;

class RangeAggTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANGE_AGG' => RangeAgg::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'basic usage' => 'SELECT range_agg(c0_.int4Range) AS sclr_0 FROM ContainsRanges c0_',
            'with DISTINCT' => 'SELECT range_agg(DISTINCT c0_.int4Range) AS sclr_0 FROM ContainsRanges c0_',
            'with ORDER BY' => 'SELECT range_agg(c0_.int4Range ORDER BY c0_.int4Range ASC) AS sclr_0 FROM ContainsRanges c0_',
            'with ORDER BY DESC' => 'SELECT range_agg(c0_.int4Range ORDER BY c0_.int4Range DESC) AS sclr_0 FROM ContainsRanges c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'basic usage' => \sprintf('SELECT RANGE_AGG(e.int4Range) FROM %s e', ContainsRanges::class),
            'with DISTINCT' => \sprintf('SELECT RANGE_AGG(DISTINCT e.int4Range) FROM %s e', ContainsRanges::class),
            'with ORDER BY' => \sprintf('SELECT RANGE_AGG(e.int4Range ORDER BY e.int4Range) FROM %s e', ContainsRanges::class),
            'with ORDER BY DESC' => \sprintf('SELECT RANGE_AGG(e.int4Range ORDER BY e.int4Range DESC) FROM %s e', ContainsRanges::class),
        ];
    }
}
