<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach;
use PHPUnit\Framework\Attributes\Test;

class JsonEachTest extends JsonTestCase
{
    use PostgresTupleParsingTrait;

    protected function getStringFunctions(): array
    {
        return [
            'JSON_EACH' => JsonEach::class,
        ];
    }

    #[Test]
    public function extracts_key_value_pairs_from_standard_json_object(): void
    {
        $dql = 'SELECT JSON_EACH(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);

        // Extract keys from PostgreSQL tuple results by processing each row individually
        $extractedKeys = [];
        foreach ($result as $row) {
            $this->assertIsArray($row, 'Query result row should be an array');
            $key = $this->extractKeysFromTupleResult($row);
            $extractedKeys[] = $key;
        }

        // Verify all expected keys are present
        $expectedKeys = ['name', 'age', 'address', 'tags'];
        $this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);

        // Verify we extracted the correct number of keys
        $this->assertCount(4, $extractedKeys, 'Should extract exactly 4 keys from JSON object');
    }

    #[Test]
    public function returns_empty_result_for_empty_object(): void
    {
        $dql = 'SELECT JSON_EACH(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 4';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }

    #[Test]
    public function extracts_key_value_pairs_from_alternative_json_object(): void
    {
        $dql = 'SELECT JSON_EACH(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);

        // Verify we get the expected number of key-value pairs
        $this->assertCount(4, $result, 'Should extract 4 key-value pairs from alternative JSON object');

        // Validate tuple structure and extract keys by processing each row individually
        $extractedKeys = [];
        foreach ($result as $row) {
            $this->assertIsArray($row, 'Query result row should be an array');
            $this->assertValidTupleStructure($row);
            $key = $this->extractKeysFromTupleResult($row);
            $extractedKeys[] = $key;
        }

        // Verify all expected keys are present
        $expectedKeys = ['name', 'age', 'address', 'tags'];
        $this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);
    }

    #[Test]
    public function extracts_key_value_pairs_when_json_contains_null_values(): void
    {
        $dql = 'SELECT JSON_EACH(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 5';
        $result = $this->executeDqlQuery($dql);

        // Verify we get the expected number of key-value pairs even with null values
        $this->assertCount(4, $result, 'Should extract 4 key-value pairs even when JSON contains null values');

        // Validate tuple structure and extract keys by processing each row individually
        $extractedKeys = [];
        foreach ($result as $row) {
            $this->assertIsArray($row, 'Query result row should be an array');
            $this->assertValidTupleStructure($row);
            $key = $this->extractKeysFromTupleResult($row);
            $extractedKeys[] = $key;
        }

        // Verify all expected keys are present
        $expectedKeys = ['name', 'age', 'address', 'tags'];
        $this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);
    }

    #[Test]
    public function extracts_key_value_pairs_when_json_contains_empty_array(): void
    {
        $dql = 'SELECT JSON_EACH(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);

        // Verify we get the expected number of key-value pairs even with empty arrays
        $this->assertCount(4, $result, 'Should extract 4 key-value pairs even when JSON contains empty arrays');

        // Validate tuple structure and extract keys by processing each row individually
        $extractedKeys = [];
        foreach ($result as $row) {
            $this->assertIsArray($row, 'Query result row should be an array');
            $this->assertValidTupleStructure($row);
            $key = $this->extractKeysFromTupleResult($row);
            $extractedKeys[] = $key;
        }

        // Verify all expected keys are present
        $expectedKeys = ['name', 'age', 'address', 'tags'];
        $this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);
    }
}
