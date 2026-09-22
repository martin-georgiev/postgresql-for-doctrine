<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asind;
use PHPUnit\Framework\Attributes\Test;

final class AsindTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASIND' => Asind::class,
        ];
    }

    #[Test]
    public function returns_the_arc_sine_in_degrees_from_a_numeric_literal(): void
    {
        $dql = 'SELECT ASIND(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(90.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_arc_sine_in_degrees_from_an_entity_field(): void
    {
        $dql = 'SELECT ASIND(n.decimal2 / 100.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(11.829499048679391, $result[0]['result']);
    }
}
