<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Asind;
use PHPUnit\Framework\Attributes\Test;

class AsindTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ASIND' => Asind::class,
        ];
    }

    #[Test]
    public function asind_of_one(): void
    {
        $dql = 'SELECT ASIND(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(90.0, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function asind_with_entity_property(): void
    {
        $dql = 'SELECT ASIND(n.decimal2 / 100.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(11.829499048679, $result[0]['result'], 0.000001);
    }
}
