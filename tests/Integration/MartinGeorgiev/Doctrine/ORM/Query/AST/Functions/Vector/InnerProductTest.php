<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\InnerProduct;
use PHPUnit\Framework\Attributes\Test;

class InnerProductTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INNER_PRODUCT' => InnerProduct::class,
        ];
    }

    #[Test]
    public function returns_correct_inner_product(): void
    {
        $dql = "SELECT INNER_PRODUCT(t.vector1, '[1,2,3]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsVectors t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(14.0, $result[0]['result']);
    }

    #[Test]
    public function returns_zero_for_orthogonal_vectors(): void
    {
        $dql = 'SELECT INNER_PRODUCT(t.vector1, t.vector2) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsVectors t
                WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.0, $result[0]['result']);
    }
}
