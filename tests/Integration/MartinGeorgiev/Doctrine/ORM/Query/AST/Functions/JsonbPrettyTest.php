<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbPretty;
use PHPUnit\Framework\Attributes\Test;

class JsonbPrettyTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_PRETTY' => JsonbPretty::class,
        ];
    }

    #[Test]
    public function formats_jsonb_as_pretty_string(): void
    {
        $dql = 'SELECT JSONB_PRETTY(t.jsonbObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);

        $prettyJson = $result[0]['result'];

        // Verify that the result is valid JSON
        $decoded = \json_decode($prettyJson, true);
        $this->assertNotNull($decoded, 'Result should be valid JSON');
        $this->assertIsArray($decoded);

        // Verify that the JSON contains the expected structure
        $this->assertArrayHasKey('name', $decoded);
        $this->assertArrayHasKey('age', $decoded);
        $this->assertArrayHasKey('address', $decoded);
        $this->assertArrayHasKey('tags', $decoded);

        // Verify that the JSON is actually formatted with indentation (pretty-printed)
        $this->assertStringContainsString("\n", $prettyJson, 'Pretty JSON should contain newlines');
        $this->assertStringContainsString('    ', $prettyJson, 'Pretty JSON should contain indentation spaces');

        // Verify that the formatted JSON is different from a compact version
        $compactJson = \json_encode($decoded);
        $this->assertNotEquals($compactJson, $prettyJson, 'Pretty JSON should be different from compact JSON');

        // Verify that the content is preserved (round-trip test)
        $this->assertEquals($decoded, \json_decode($compactJson, true), 'Content should be preserved in pretty formatting');
    }
}
