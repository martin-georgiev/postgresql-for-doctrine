<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Erfc;
use PHPUnit\Framework\Attributes\Test;

final class ErfcTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ERFC' => Erfc::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'ERFC');
    }

    #[Test]
    public function calculates_erfc_of_literal(): void
    {
        $dql = 'SELECT ERFC(1.0) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0.15729920705028513, $result[0]['result']);
    }

    #[Test]
    public function calculates_erfc_with_entity_property(): void
    {
        $dql = 'SELECT ERFC(n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(0.0, $result[0]['result'], 1e-44);
    }
}
