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

    /**
     * @param array<int, array<string, mixed>> $result Query results from executeDqlQuery()
     * @param int $expectedCount Expected number of key-value pairs
     *
     * @return array<int, string> Array of extracted keys
     */
    private function extractAndValidateKeysFromJsonEachResult(array $result, int $expectedCount): array
    {
        $this->assertCount($expectedCount, $result);

        $extractedKeys = [];
        foreach ($result as $row) {
            $this->assertIsArray($row, 'Query result row should be an array');
            $this->assertValidTupleStructure($row);
            $extractedKeys[] = $this->extractKeysFromTupleResult($row);
        }

        return $extractedKeys;
    }

    /**
     * @param array<int, string> $extractedKeys
     * @param array<int, string> $expectedKeys Keys expected to be among the extracted keys
     */
    private function assertExtractedKeys(array $extractedKeys, array $expectedKeys): void
    {
        $expectedCount = \count($expectedKeys);
        $this->assertExpectedKeysArePresent($extractedKeys, $expectedKeys);
        $this->assertCount($expectedCount, $extractedKeys, \sprintf('Should extract exactly %d keys from JSON object', $expectedCount));
    }

    #[Test]
    public function extracts_key_value_pairs_from_standard_json_object(): void
    {
        $dql = 'SELECT JSON_EACH(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);

        $extractedKeys = $this->extractAndValidateKeysFromJsonEachResult($result, 4);
        $this->assertExtractedKeys($extractedKeys, ['name', 'age', 'address', 'tags']);
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

        $extractedKeys = $this->extractAndValidateKeysFromJsonEachResult($result, 4);
        $this->assertExtractedKeys($extractedKeys, ['name', 'age', 'address', 'tags']);
    }

    #[Test]
    public function extracts_key_value_pairs_when_json_contains_null_values(): void
    {
        $dql = 'SELECT JSON_EACH(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 5';
        $result = $this->executeDqlQuery($dql);

        $extractedKeys = $this->extractAndValidateKeysFromJsonEachResult($result, 4);
        $this->assertExtractedKeys($extractedKeys, ['name', 'age', 'address', 'tags']);
    }

    #[Test]
    public function extracts_key_value_pairs_when_json_contains_empty_array(): void
    {
        $dql = 'SELECT JSON_EACH(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);

        $extractedKeys = $this->extractAndValidateKeysFromJsonEachResult($result, 4);
        $this->assertExtractedKeys($extractedKeys, ['name', 'age', 'address', 'tags']);
    }
}
