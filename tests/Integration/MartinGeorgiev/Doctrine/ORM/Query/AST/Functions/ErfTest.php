<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erf;
use PHPUnit\Framework\Attributes\Test;

class ErfTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ERF' => Erf::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'ERF');
    }

    #[Test]
    public function can_calculate_erf_of_literal(): void
    {
        $dql = 'SELECT ERF(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.8427, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function can_calculate_erf_with_entity_property(): void
    {
        $dql = 'SELECT ERF(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0, $result[0]['result'], 0.0001);
    }
}
