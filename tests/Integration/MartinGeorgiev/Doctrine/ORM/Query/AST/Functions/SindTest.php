<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sind;
use PHPUnit\Framework\Attributes\Test;

final class SindTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIND' => Sind::class,
        ];
    }

    #[Test]
    public function returns_the_sine_of_degrees_from_a_literal(): void
    {
        $dql = 'SELECT SIND(90.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_sine_of_degrees_from_an_entity_field(): void
    {
        $dql = 'SELECT SIND(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.1822355254921475, $result[0]['result']);
    }
}
