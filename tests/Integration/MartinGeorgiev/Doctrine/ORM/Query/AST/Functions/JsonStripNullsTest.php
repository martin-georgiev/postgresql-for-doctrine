<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonStripNulls;
use PHPUnit\Framework\Attributes\Test;

class JsonStripNullsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_STRIP_NULLS' => JsonStripNulls::class,
        ];
    }

    #[Test]
    public function json_strip_nulls(): void
    {
        $dql = 'SELECT JSON_STRIP_NULLS(t.jsonObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }

    #[Test]
    public function json_strip_nulls_with_null_values(): void
    {
        $dql = 'SELECT JSON_STRIP_NULLS(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 5';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringNotContainsString('"age": null', $result[0]['result']);
        $this->assertStringNotContainsString('"zip": null', $result[0]['result']);
    }

    #[Test]
    public function json_strip_nulls_with_null_value_treatment_parameter(): void
    {
        $this->requirePostgresVersion(180000, 'null_value_treatment parameter for json_strip_nulls');

        $dql = "SELECT JSON_STRIP_NULLS(t.jsonObject1, 'true') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 5";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }
}
