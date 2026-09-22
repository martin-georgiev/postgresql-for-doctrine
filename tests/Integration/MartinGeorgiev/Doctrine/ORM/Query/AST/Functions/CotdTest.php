<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cotd;
use PHPUnit\Framework\Attributes\Test;

final class CotdTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COTD' => Cotd::class,
        ];
    }

    #[Test]
    public function returns_the_cotangent_in_degrees_from_a_numeric_literal(): void
    {
        $dql = 'SELECT COTD(45.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_cotangent_in_degrees_from_an_entity_field(): void
    {
        $dql = 'SELECT COTD(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(5.395517174319137, $result[0]['result']);
    }
}
