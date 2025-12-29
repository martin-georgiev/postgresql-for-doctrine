<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonObjectKeys;
use PHPUnit\Framework\Attributes\Test;

class JsonObjectKeysTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_OBJECT_KEYS' => JsonObjectKeys::class,
        ];
    }

    #[Test]
    public function extracts_object_keys_from_json(): void
    {
        $dql = 'SELECT JSON_OBJECT_KEYS(t.jsonObject1) as result
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
            $this->assertContains($expectedKey, $foundKeys);
        }
    }

    #[Test]
    public function returns_empty_for_empty_object(): void
    {
        $dql = 'SELECT JSON_OBJECT_KEYS(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 4';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}

