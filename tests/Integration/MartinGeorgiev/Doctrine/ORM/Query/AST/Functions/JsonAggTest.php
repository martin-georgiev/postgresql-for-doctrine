<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonAgg;
use PHPUnit\Framework\Attributes\Test;

final class JsonAggTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_AGG' => JsonAgg::class,
        ];
    }

    #[Test]
    public function returns_the_aggregated_json_from_an_entity_field(): void
    {
        $dql = 'SELECT JSON_AGG(t.textArray) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertCount(1, $decoded);
        $this->assertSame(['apple', 'banana', 'orange'], $decoded[0]);
    }
}
