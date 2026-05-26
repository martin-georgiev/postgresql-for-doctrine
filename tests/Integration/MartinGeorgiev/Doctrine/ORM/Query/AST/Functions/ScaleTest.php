<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Scale;
use PHPUnit\Framework\Attributes\Test;

final class ScaleTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SCALE' => Scale::class,
        ];
    }

    #[Test]
    public function calculates_scale_of_literal(): void
    {
        $dql = 'SELECT SCALE(8.41) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2, $result[0]['result']);
    }

    #[Test]
    public function calculates_scale_with_entity_property(): void
    {
        $dql = 'SELECT SCALE(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
