<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cosd;
use PHPUnit\Framework\Attributes\Test;

final class CosdTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'COSD' => Cosd::class,
        ];
    }

    #[Test]
    public function returns_the_cosine_in_degrees_from_a_numeric_literal(): void
    {
        $dql = 'SELECT COSD(0.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_cosine_in_degrees_from_an_entity_field(): void
    {
        $dql = 'SELECT COSD(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.9832549075639546, $result[0]['result']);
    }
}
