<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEachText;
use PHPUnit\Framework\Attributes\Test;

final class JsonEachTextTest extends JsonTestCase
{
    use PostgresTupleParsingTrait;

    protected function getStringFunctions(): array
    {
        return [
            'JSON_EACH_TEXT' => JsonEachText::class,
        ];
    }

    #[Test]
    public function returns_the_key_value_pairs_as_text_from_an_entity_field(): void
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
}
