<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tand;
use PHPUnit\Framework\Attributes\Test;

final class TandTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TAND' => Tand::class,
        ];
    }

    #[Test]
    public function returns_the_tangent_of_degrees_from_a_literal(): void
    {
        $dql = 'SELECT TAND(45.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_tangent_of_degrees_from_an_entity_field(): void
    {
        $dql = 'SELECT TAND(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.1853390449315344, $result[0]['result']);
    }
}
