<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\CosineDistance;
use PHPUnit\Framework\Attributes\Test;

class CosineDistanceTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COSINE_DISTANCE' => CosineDistance::class,
        ];
    }

    #[Test]
    public function returns_zero_for_identical_vectors(): void
    {
        $dql = "SELECT COSINE_DISTANCE(t.vector1, '[1,2,3]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsVectors t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }

    #[Test]
    public function returns_one_for_orthogonal_vectors(): void
    {
        $dql = 'SELECT COSINE_DISTANCE(t.vector1, t.vector2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsVectors t
                WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
