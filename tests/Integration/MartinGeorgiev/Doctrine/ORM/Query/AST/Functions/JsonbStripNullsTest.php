<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls;

class JsonbStripNullsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_STRIP_NULLS' => JsonbStripNulls::class,
        ];
    }

    public function test_jsonb_strip_nulls(): void
    {
        $dql = 'SELECT JSONB_STRIP_NULLS(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }

    public function test_jsonb_strip_nulls_with_null_values(): void
    {
        $dql = 'SELECT JSONB_STRIP_NULLS(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 5';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringNotContainsString('"age": null', $result[0]['result']);
        $this->assertStringNotContainsString('"zip": null', $result[0]['result']);
    }
}
