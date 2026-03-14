<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atand;
use PHPUnit\Framework\Attributes\Test;

class AtandTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAND' => Atand::class,
        ];
    }

    #[Test]
    public function atand_of_one(): void
    {
        $dql = 'SELECT ATAND(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(45.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function atand_with_entity_property(): void
    {
        $dql = 'SELECT ATAND(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(84.559667968994, $result[0]['result'], 0.000001);
    }
}
