<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PercentileDisc;
use PHPUnit\Framework\Attributes\Test;

final class PercentileDiscTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PERCENTILE_DISC' => PercentileDisc::class,
        ];
    }

    #[Test]
    public function returns_the_discrete_percentile_from_an_entity_field(): void
    {
        $dql = 'SELECT PERCENTILE_DISC(0.5 WITHIN GROUP ORDER BY n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('10.5', $result[0]['result']);
    }
}
