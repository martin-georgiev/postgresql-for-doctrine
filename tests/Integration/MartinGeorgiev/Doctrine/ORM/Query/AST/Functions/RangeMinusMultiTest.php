<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeMinusMulti;
use PHPUnit\Framework\Attributes\Test;

final class RangeMinusMultiTest extends RangeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(190000, 'range_minus_multi function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'RANGE_MINUS_MULTI' => RangeMinusMulti::class,
        ];
    }

    #[Test]
    public function returns_the_remaining_ranges_from_a_range_literal(): void
    {
        $dql = "SELECT RANGE_MINUS_MULTI(t.int4Range, '[3,4)') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(['[1,3)', '[4,10)'], \array_column($result, 'result'));
    }

    #[Test]
    public function returns_the_remaining_ranges_from_entity_fields(): void
    {
        $dql = 'SELECT RANGE_MINUS_MULTI(t.int4Range, u.int4Range) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges t, Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges u WHERE t.id = 1 AND u.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(['[1,5)'], \array_column($result, 'result'));
    }
}
