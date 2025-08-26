<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys;
use PHPUnit\Framework\Attributes\Test;

class JsonbObjectKeysTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_OBJECT_KEYS' => JsonbObjectKeys::class,
        ];
    }

    #[Test]
    public function extracts_object_keys_from_jsonb(): void
    {
        $dql = 'SELECT JSONB_OBJECT_KEYS(t.jsonbObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);

        $foundKeys = [];
        foreach ($result as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('result', $row);
            $this->assertIsString($row['result']);
            $foundKeys[] = $row['result'];
        }

        $expectedKeys = ['name', 'age', 'address', 'tags'];
        foreach ($expectedKeys as $expectedKey) {
            $this->assertContains($expectedKey, $foundKeys, \sprintf("Expected key '%s' should be present in the extracted keys", $expectedKey));
        }
    }
}
