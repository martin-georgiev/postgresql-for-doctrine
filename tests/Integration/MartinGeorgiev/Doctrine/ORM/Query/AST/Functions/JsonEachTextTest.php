<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText;
use PHPUnit\Framework\Attributes\Test;

class JsonEachTextTest extends JsonTestCase
{
    use PostgresTupleParsingTrait;

    protected function getStringFunctions(): array
    {
        return [
            'JSON_EACH_TEXT' => JsonEachText::class,
        ];
    }

    #[Test]
    public function extracts_key_value_pairs_as_text_from_json(): void
    {
        $dql = 'SELECT JSON_EACH_TEXT(t.jsonObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);

        $extractedKeys = [];
        foreach ($result as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('result', $row);
            $extractedKeys[] = $this->extractKeysFromTupleResult($row);
        }

        $expectedKeys = ['name', 'age', 'address', 'tags'];
        foreach ($expectedKeys as $expectedKey) {
            $this->assertContains($expectedKey, $extractedKeys);
        }
    }

    #[Test]
    public function returns_empty_result_for_empty_object(): void
    {
        $dql = 'SELECT JSON_EACH_TEXT(t.jsonObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 4';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}
