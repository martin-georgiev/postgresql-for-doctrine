<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log;

class LogTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LOG' => Log::class,
        ];
    }

    public function test_log(): void
    {
        $dql = 'SELECT LOG(10, 100) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(2.0, $result[0]['result'], 0.0001);
    }

    public function test_log_with_entity_property(): void
    {
        $dql = 'SELECT LOG(10, n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEqualsWithDelta(1.0211892990699381, $result[0]['result'], 0.000001);
    }
}
