<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * PostgreSQL functions (like json(b)_each) return tuples in the format "(key,value)".
 * This trait provides reusable methods to parse these tuples and extract meaningful data for semantic testing rather than format validation.
 */
trait PostgresTupleParsingTrait
{
    /**
     * Extracts a single key from a PostgreSQL tuple format result row.
     *
     * @param array<string, mixed> $queryResult Single query result row
     * @param string $resultKeyName Name of the result key in the query result
     *
     * @return string The extracted key from the tuple
     */
    protected function extractKeysFromTupleResult(array $queryResult, string $resultKeyName = 'result'): string
    {
        $this->assertArrayHasKey($resultKeyName, $queryResult, \sprintf('Query result should contain "%s" key', $resultKeyName));
        $this->assertIsString($queryResult[$resultKeyName], 'Query result should be a string');

        $key = $this->parseKeyFromTuple($queryResult[$resultKeyName]);
        $this->assertNotNull($key, 'Should be able to extract key from tuple result');

        return $key;
    }

    /**
     * Extracts a key-value pair from a PostgreSQL tuple format result row.
     *
     * @param array<string, mixed> $queryResult Single query result row
     * @param string $resultKeyName Name of the result key in the query result
     *
     * @return array{key: string, value: string} The extracted key-value pair from the tuple
     */
    protected function extractKeyValuePairsFromTupleResult(array $queryResult, string $resultKeyName = 'result'): array
    {
        $this->assertArrayHasKey($resultKeyName, $queryResult, \sprintf('Query result should contain "%s" key', $resultKeyName));
        $this->assertIsString($queryResult[$resultKeyName], 'Query result should be a string');

        $pair = $this->parseKeyValueFromTuple($queryResult[$resultKeyName]);
        $this->assertNotNull($pair, 'Should be able to extract key-value pair from tuple result');

        return $pair;
    }

    /**
     * Validates that all expected keys are present in the extracted results.
     *
     * @param array<int, string> $extractedKeys Keys extracted from tuple results
     * @param array<int, string> $expectedKeys Keys that should be present
     */
    protected function assertExpectedKeysArePresent(array $extractedKeys, array $expectedKeys): void
    {
        foreach ($expectedKeys as $expectedKey) {
            $this->assertContains(
                $expectedKey,
                $extractedKeys,
                \sprintf("Expected key '%s' should be extracted from JSON object", $expectedKey)
            );
        }
    }

    /**
     * Validates that a query result row contains a valid PostgreSQL tuple structure.
     *
     * @param array<string, mixed> $queryResult Single query result row
     * @param string $resultKeyName Name of the result key in the query result
     */
    protected function assertValidTupleStructure(array $queryResult, string $resultKeyName = 'result'): void
    {
        $this->assertArrayHasKey($resultKeyName, $queryResult, \sprintf('Query result should contain "%s" key', $resultKeyName));
        $this->assertIsString($queryResult[$resultKeyName], 'Query result should be a string');

        $this->assertTrue(
            $this->isValidTupleFormat($queryResult[$resultKeyName]),
            \sprintf("Result '%s' should be a valid PostgreSQL tuple format", $queryResult[$resultKeyName])
        );
    }

    /**
     * @param string $tupleString PostgreSQL tuple in format (key,value)
     *
     * @return string|null The extracted key, or null if parsing fails
     */
    private function parseKeyFromTuple(string $tupleString): ?string
    {
        if (!$this->isValidTupleFormat($tupleString)) {
            return null;
        }

        // Remove outer parentheses
        $tupleContent = \trim($tupleString, '()');

        // Find the first comma that separates key from value
        $commaPos = \strpos($tupleContent, ',');
        if ($commaPos === false) {
            return null;
        }

        return \substr($tupleContent, 0, $commaPos);
    }

    /**
     * @param string $tupleString PostgreSQL tuple in format (key,value)
     *
     * @return array{key: string, value: string}|null The extracted key-value pair, or null if parsing fails
     */
    private function parseKeyValueFromTuple(string $tupleString): ?array
    {
        if (!$this->isValidTupleFormat($tupleString)) {
            return null;
        }

        // Remove outer parentheses
        $tupleContent = \trim($tupleString, '()');

        // Find the first comma that separates key from value
        $commaPos = \strpos($tupleContent, ',');
        if ($commaPos === false) {
            return null;
        }

        $key = \substr($tupleContent, 0, $commaPos);
        $value = \substr($tupleContent, $commaPos + 1);

        return [
            'key' => $key,
            'value' => $value,
        ];
    }

    private function isValidTupleFormat(string $tupleString): bool
    {
        // Must start with '(' and end with ')'
        if (!\str_starts_with($tupleString, '(') || !\str_ends_with($tupleString, ')')) {
            return false;
        }

        // Must contain at least one comma (key,value separator)
        if (!\str_contains($tupleString, ',')) {
            return false;
        }

        // Must have content between parentheses
        $content = \trim($tupleString, '()');

        return $content !== '' && $content !== '0';
    }
}
