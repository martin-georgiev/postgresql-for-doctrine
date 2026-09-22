<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ceil;
use PHPUnit\Framework\Attributes\Test;

final class CeilTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CEIL' => Ceil::class,
        ];
    }

    #[Test]
    public function returns_the_ceiling_from_a_numeric_literal(): void
    {
        $dql = 'SELECT CEIL(10.5) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(11, $result[0]['result']);
    }

    #[Test]
    public function returns_the_ceiling_from_an_entity_field(): void
    {
        $dql = 'SELECT CEIL(t.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(11, $result[0]['result']);
    }
}
