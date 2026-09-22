<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbStripNulls;
use PHPUnit\Framework\Attributes\Test;

final class JsonbStripNullsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_STRIP_NULLS' => JsonbStripNulls::class,
        ];
    }

    #[Test]
    public function returns_the_jsonb_without_nulls_from_an_entity_field(): void
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
    public function returns_the_jsonb_without_nulls_from_an_entity_field_with_null_value_treatment(): void
    {
        $this->requirePostgresVersion(180000, 'null_value_treatment parameter for jsonb_strip_nulls');

        $dql = "SELECT JSONB_STRIP_NULLS(t.jsonbObject1, 'true') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 5";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }
}
