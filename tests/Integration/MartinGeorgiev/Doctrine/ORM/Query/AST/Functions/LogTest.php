<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log;
use PHPUnit\Framework\Attributes\Test;

class LogTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LOG' => Log::class,
        ];
    }

    #[Test]
    public function log(): void
    {
        $dql = 'SELECT LOG(10, 100) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.0, $result[0]['result'], 0.0001);
    }

    #[Test]
    public function log_with_entity_property(): void
    {
        $dql = 'SELECT LOG(10, n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0211892990699381, $result[0]['result'], 0.000001);
    }
}
