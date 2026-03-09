<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\L2Distance;
use PHPUnit\Framework\Attributes\Test;

class L2DistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'L2_DISTANCE' => L2Distance::class,
        ];
    }

    #[Test]
    public function returns_zero_for_identical_vectors(): void
    {
        $dql = "SELECT L2_DISTANCE(t.vector1, '[1,2,3]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsVectors t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_positive_distance_for_different_vectors(): void
    {
        $dql = 'SELECT L2_DISTANCE(t.vector1, t.vector2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsVectors t
                WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.4142, $result[0]['result']);
    }
}
