<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lgamma;
use PHPUnit\Framework\Attributes\Test;

final class LgammaTest extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'lgamma function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'LGAMMA' => Lgamma::class,
        ];
    }

    #[Test]
    public function returns_the_log_gamma_from_a_numeric_literal(): void
    {
        $dql = 'SELECT LGAMMA(5) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.1780538303479458, $result[0]['result']);
    }

    #[Test]
    public function returns_the_log_gamma_from_an_entity_field(): void
    {
        $dql = 'SELECT LGAMMA(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(12.80182748008147, $result[0]['result']);
    }
}
