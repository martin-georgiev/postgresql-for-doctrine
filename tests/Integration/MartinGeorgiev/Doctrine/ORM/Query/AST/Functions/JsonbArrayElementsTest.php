<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements;
use PHPUnit\Framework\Attributes\Test;

class JsonbArrayElementsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_ARRAY_ELEMENTS' => JsonbArrayElements::class,
        ];
    }

    #[Test]
    public function can_expand_jsonb_array_to_rows(): void
    {
        $dql = "SELECT JSONB_ARRAY_ELEMENTS(t.jsonbObject1->'tags') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(2, $result);

        $values = \array_map(fn ($row) => \json_decode($row['result'], true), $result);
        $this->assertContains('developer', $values);
        $this->assertContains('manager', $values);
    }

    #[Test]
    public function returns_empty_for_empty_array(): void
    {
        $dql = "SELECT JSONB_ARRAY_ELEMENTS(t.jsonbObject1->'tags') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 3";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }
}

