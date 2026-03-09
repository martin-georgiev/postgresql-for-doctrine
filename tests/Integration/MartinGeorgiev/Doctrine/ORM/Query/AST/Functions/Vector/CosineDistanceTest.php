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
        $dql = "SELECT COSINE_DISTANCE('[1,2,3]', '[1,2,3]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function returns_one_for_orthogonal_vectors(): void
    {
        $dql = "SELECT COSINE_DISTANCE('[1,0,0]', '[0,1,0]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.0001);
    }
}
