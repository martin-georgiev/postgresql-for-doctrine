<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees;
use PHPUnit\Framework\Attributes\Test;

final class DegreesTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DEGREES' => Degrees::class,
        ];
    }

    #[Test]
    public function returns_the_degrees_from_a_numeric_literal(): void
    {
        $dql = 'SELECT DEGREES(3.141592653589793) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(180.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_degrees_from_an_entity_field(): void
    {
        $dql = 'SELECT DEGREES(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(601.6056848873644, $result[0]['result']);
    }
}
