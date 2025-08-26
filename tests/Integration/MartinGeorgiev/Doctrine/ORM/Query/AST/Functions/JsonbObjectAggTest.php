<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg;
use PHPUnit\Framework\Attributes\Test;

class JsonbObjectAggTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_OBJECT_AGG' => JsonbObjectAgg::class,
        ];
    }

    #[Test]
    public function can_aggregate_key_value_pairs_to_jsonb_object(): void
    {
        $dql = "SELECT JSONB_OBJECT_AGG('key', 'value') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame(['key' => 'value'], $decoded);
    }
}
