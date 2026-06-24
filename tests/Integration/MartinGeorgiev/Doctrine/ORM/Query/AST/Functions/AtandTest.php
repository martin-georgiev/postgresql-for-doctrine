<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atand;
use PHPUnit\Framework\Attributes\Test;

final class AtandTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAND' => Atand::class,
        ];
    }

    #[Test]
    public function calculates_atand_of_literal(): void
    {
        $dql = 'SELECT ATAND(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(45.0, $result[0]['result']);
    }

    #[Test]
    public function calculates_atand_with_entity_property(): void
    {
        $dql = 'SELECT ATAND(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(84.55966796899449, $result[0]['result']);
    }
}
