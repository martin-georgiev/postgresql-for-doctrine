<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Row;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RowToJson;
use PHPUnit\Framework\Attributes\Test;

class RowToJsonTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ROW_TO_JSON' => RowToJson::class,
            'ROW' => Row::class,
        ];
    }

    #[Test]
    public function can_convert_row_to_json(): void
    {
        $dql = 'SELECT ROW_TO_JSON(ROW(t.id, t.jsonObject1)) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('f1', $decoded);
        $this->assertSame(1, $decoded['f1']);
    }

    #[Test]
    public function can_convert_row_with_multiple_fields(): void
    {
        $dql = 'SELECT ROW_TO_JSON(ROW(t.id, t.jsonbObject1, t.jsonbObject2)) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertArrayHasKey('f1', $decoded);
        $this->assertArrayHasKey('f2', $decoded);
        $this->assertArrayHasKey('f3', $decoded);
    }
}
