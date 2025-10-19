<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectAgg;
use PHPUnit\Framework\Attributes\Test;

class JsonObjectAggTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_OBJECT_AGG' => JsonObjectAgg::class,
        ];
    }

    #[Test]
    public function can_aggregate_key_value_pairs_to_json_object(): void
    {
        $dql = "SELECT JSON_OBJECT_AGG('key', t.jsonObject1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);

        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('key', $decoded);

        $keyData = $decoded['key'];
        $this->assertIsArray($keyData);

        $this->assertArrayHasKey('age', $keyData);
        $this->assertSame(30, $keyData['age']);

        $this->assertArrayHasKey('name', $keyData);
        $this->assertSame('Micky', $keyData['name']);

        $this->assertArrayHasKey('tags', $keyData);
        $this->assertSame([], $keyData['tags']);

        $this->assertArrayHasKey('address', $keyData);
        $this->assertSame(['city' => 'New York'], $keyData['address']);
    }
}
