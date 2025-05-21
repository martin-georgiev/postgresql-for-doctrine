<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText;

class JsonbArrayElementsTextTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_ARRAY_ELEMENTS_TEXT' => JsonbArrayElementsText::class,
        ];
    }

    public function test_jsonb_array_elements_text(): void
    {
        $dql = 'SELECT JSONB_ARRAY_ELEMENTS_TEXT(t.object1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertSame('developer', $result[0]['result']);
        $this->assertSame('manager', $result[1]['result']);
    }

    public function test_jsonb_array_elements_text_with_empty_array(): void
    {
        $dql = 'SELECT JSONB_ARRAY_ELEMENTS_TEXT(t.object1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
