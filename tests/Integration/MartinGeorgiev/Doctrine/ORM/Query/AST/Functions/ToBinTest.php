<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToBin;
use PHPUnit\Framework\Attributes\Test;

final class ToBinTest extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'to_bin function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'TO_BIN' => ToBin::class,
        ];
    }

    #[Test]
    public function returns_the_binary_representation_from_a_numeric_literal(): void
    {
        $dql = 'SELECT TO_BIN(255) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('11111111', $result[0]['result']);
    }

    #[Test]
    public function returns_the_binary_representation_from_an_entity_field(): void
    {
        $dql = 'SELECT TO_BIN(n.integer1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1010', $result[0]['result']);
    }
}
