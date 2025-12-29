<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToJsonb;
use PHPUnit\Framework\Attributes\Test;

class ToJsonbTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_JSONB' => ToJsonb::class,
        ];
    }

    #[Test]
    public function can_convert_jsonb_column_to_jsonb(): void
    {
        $dql = 'SELECT TO_JSONB(t.jsonbObject1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame('John', $decoded['name']);
    }

    #[Test]
    public function can_convert_json_column_to_jsonb(): void
    {
        $dql = 'SELECT TO_JSONB(t.jsonObject1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame('John', $decoded['name']);
    }
}
