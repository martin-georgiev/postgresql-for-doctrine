<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectAgg;
use PHPUnit\Framework\Attributes\Test;

final class JsonbObjectAggTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_OBJECT_AGG' => JsonbObjectAgg::class,
        ];
    }

    #[Test]
    public function returns_the_aggregated_jsonb_object_from_an_entity_field(): void
    {
        $dql = "SELECT JSONB_OBJECT_AGG('key', t.jsonbObject1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"key": {"age": 30, "name": "John", "tags": ["developer", "manager"], "address": {"city": "New York"}}}', $result[0]['result']);
    }
}
