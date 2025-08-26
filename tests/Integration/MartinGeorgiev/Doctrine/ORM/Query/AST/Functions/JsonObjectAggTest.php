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
    public function json_object_agg_simple(): void
    {
        $dql = "SELECT JSON_OBJECT_AGG('key', t.object1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame([
            'key' => [
                'age' => 30,
                'name' => 'Micky',
                'tags' => [],
                'address' => ['city' => 'New York'],
            ],
        ], $decoded);
    }
}
