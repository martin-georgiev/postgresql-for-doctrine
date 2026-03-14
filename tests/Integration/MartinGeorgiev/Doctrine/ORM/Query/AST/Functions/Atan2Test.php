<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Atan2;
use PHPUnit\Framework\Attributes\Test;

class Atan2Test extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ATAN2' => Atan2::class,
        ];
    }

    #[Test]
    public function atan2_of_one_one(): void
    {
        $dql = 'SELECT ATAN2(1.0, 1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.7853981, $result[0]['result'], 0.000001);
    }

    #[Test]
    public function atan2_with_entity_properties(): void
    {
        $dql = 'SELECT ATAN2(n.decimal1, n.decimal2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.47335604183492, $result[0]['result'], 0.000001);
    }
}
