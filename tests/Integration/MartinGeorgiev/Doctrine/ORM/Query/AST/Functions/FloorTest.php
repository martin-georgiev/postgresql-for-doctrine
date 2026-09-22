<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Floor;
use PHPUnit\Framework\Attributes\Test;

final class FloorTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FLOOR' => Floor::class,
        ];
    }

    #[Test]
    public function returns_the_floor_from_a_numeric_literal(): void
    {
        $dql = 'SELECT FLOOR(10.5) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_floor_from_an_entity_field(): void
    {
        $dql = 'SELECT FLOOR(t.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10, $result[0]['result']);
    }
}
