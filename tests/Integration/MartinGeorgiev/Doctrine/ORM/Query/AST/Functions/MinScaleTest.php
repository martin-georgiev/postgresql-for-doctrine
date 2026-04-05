<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MinScale;
use PHPUnit\Framework\Attributes\Test;

class MinScaleTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MIN_SCALE' => MinScale::class,
        ];
    }

    #[Test]
    public function can_calculate_min_scale_of_literal(): void
    {
        $dql = 'SELECT MIN_SCALE(8.4100) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['result']);
    }

    #[Test]
    public function can_calculate_min_scale_with_entity_property(): void
    {
        $dql = 'SELECT MIN_SCALE(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
