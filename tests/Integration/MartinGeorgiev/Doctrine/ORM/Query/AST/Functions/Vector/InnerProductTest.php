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
        $dql = "SELECT INNER_PRODUCT('[1,2,3]', '[1,2,3]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(14.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function returns_zero_for_orthogonal_vectors(): void
    {
        $dql = "SELECT INNER_PRODUCT('[1,0,0]', '[0,1,0]') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 0.0001);
    }
}
