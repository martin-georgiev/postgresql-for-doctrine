<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach;
use PHPUnit\Framework\Attributes\Test;

class JsonbEachTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EACH' => JsonbEach::class,
        ];
    }

    #[Test]
    public function extracts_key_value_pairs_from_standard_json_object(): void
    {
        $dql = 'SELECT JSONB_EACH(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);

        $extractedKeys = [];
        foreach ($result as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('result', $row);
            $this->assertIsString($row['result']);

            $decoded = \json_decode($row['result'], true);
            if (\is_array($decoded) && isset($decoded['key'], $decoded['value'])) {
                $key = $decoded['key'];
            } else {
                $parts = \explode(':', \trim($row['result'], '{}"'));
                $key = $parts[0] ?? null;
            }

            $this->assertNotNull($key);
            $extractedKeys[] = $key;
        }

        $expectedKeys = ['name', 'age', 'address', 'tags'];
        foreach ($expectedKeys as $expectedKey) {
            $this->assertContains($expectedKey, $extractedKeys, \sprintf("Expected key '%s' should be extracted", $expectedKey));
        }
    }

    #[Test]
    public function returns_empty_result_for_empty_object(): void
    {
        $dql = 'SELECT JSONB_EACH(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 4';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }

    #[Test]
    public function extracts_key_value_pairs_from_alternative_json_object(): void
    {
        $dql = 'SELECT JSONB_EACH(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);

        foreach ($result as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('result', $row);
            $this->assertIsString($row['result']);

            $decoded = \json_decode($row['result'], true);
            $this->assertNotNull($decoded, 'Result should be valid JSON');
        }
    }

    #[Test]
    public function extracts_key_value_pairs_when_json_contains_null_values(): void
    {
        $dql = 'SELECT JSONB_EACH(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 5';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);

        foreach ($result as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('result', $row);
            $this->assertIsString($row['result']);

            $decoded = \json_decode($row['result'], true);
            $this->assertNotNull($decoded, 'Result should be valid JSON even with null values');
        }
    }

    #[Test]
    public function extracts_key_value_pairs_when_json_contains_empty_array(): void
    {
        $dql = 'SELECT JSONB_EACH(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);

        foreach ($result as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('result', $row);
            $this->assertIsString($row['result']);

            $decoded = \json_decode($row['result'], true);
            $this->assertNotNull($decoded, 'Result should be valid JSON even with empty arrays');
        }
    }
}
