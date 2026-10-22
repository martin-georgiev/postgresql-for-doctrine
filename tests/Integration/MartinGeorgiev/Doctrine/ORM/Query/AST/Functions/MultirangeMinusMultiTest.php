<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MultirangeMinusMulti;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RangeAgg;
use PHPUnit\Framework\Attributes\Test;

final class MultirangeMinusMultiTest extends RangeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(190000, 'multirange_minus_multi function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'MULTIRANGE_MINUS_MULTI' => MultirangeMinusMulti::class,
            'RANGE_AGG' => RangeAgg::class,
        ];
    }

    #[Test]
    public function returns_the_remaining_multirange_from_a_multirange_literal(): void
    {
        $dql = "SELECT MULTIRANGE_MINUS_MULTI(RANGE_AGG(t.int4Range), '{[3,4)}') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsRanges t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(['{[1,3),[4,10)}'], \array_column($result, 'result'));
    }

    #[Test]
    public function returns_the_remaining_multirange_from_entity_fields(): void
    {
        $dql = 'SELECT MULTIRANGE_MINUS_MULTI(RANGE_AGG(t.int4Range), RANGE_AGG(u.int4Range)) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges t, Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges u WHERE t.id = 1 AND u.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(['{[1,5)}'], \array_column($result, 'result'));
    }
}
