<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls;
use PHPUnit\Framework\Attributes\Test;

class JsonbStripNullsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_STRIP_NULLS' => JsonbStripNulls::class,
        ];
    }

    #[Test]
    public function jsonb_strip_nulls(): void
    {
        $dql = 'SELECT JSONB_STRIP_NULLS(t.jsonbObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }

    #[Test]
    public function jsonb_strip_nulls_with_null_values(): void
    {
        $dql = 'SELECT JSONB_STRIP_NULLS(t.jsonbObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 5';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringNotContainsString('"age": null', $result[0]['result']);
        $this->assertStringNotContainsString('"zip": null', $result[0]['result']);
    }

    #[Test]
    public function jsonb_strip_nulls_with_null_value_treatment_parameter(): void
    {
        $dql = "SELECT JSONB_STRIP_NULLS(t.jsonbObject1, 'use_json_null') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 5";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }
}
