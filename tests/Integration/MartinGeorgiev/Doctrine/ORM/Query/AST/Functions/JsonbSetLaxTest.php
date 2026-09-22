<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbSetLax;
use PHPUnit\Framework\Attributes\Test;

final class JsonbSetLaxTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_SET_LAX' => JsonbSetLax::class,
        ];
    }

    #[Test]
    public function returns_the_jsonb_with_the_value_set_from_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_SET_LAX(t.jsonbObject1, :path, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql, [
            'path' => '{name}',
            'value' => '"John Doe"',
        ]);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('name', $decoded);
        $this->assertSame('John Doe', $decoded['name']);
    }
}
