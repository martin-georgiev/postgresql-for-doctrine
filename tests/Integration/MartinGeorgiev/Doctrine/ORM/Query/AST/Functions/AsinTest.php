<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asin;
use PHPUnit\Framework\Attributes\Test;

final class AsinTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASIN' => Asin::class,
        ];
    }

    #[Test]
    public function calculates_asin_of_literal(): void
    {
        $dql = 'SELECT ASIN(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.5707963267948966, $result[0]['result']);
    }

    #[Test]
    public function calculates_asin_with_entity_property(): void
    {
        $dql = 'SELECT ASIN(n.decimal2 / 100.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.2064637072609924, $result[0]['result']);
    }
}
