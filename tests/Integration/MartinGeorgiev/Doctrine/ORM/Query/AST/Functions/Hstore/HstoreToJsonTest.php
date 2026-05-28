<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\HstoreToJson;
use PHPUnit\Framework\Attributes\Test;

class HstoreToJsonTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_TO_JSON' => HstoreToJson::class,
        ];
    }

    #[Test]
    public function converts_hstore_literal_to_json(): void
    {
        $dql = "SELECT HSTORE_TO_JSON('\"a\"=>\"1\",\"b\"=>\"2\"') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame('1', $decoded['a']);
        $this->assertSame('2', $decoded['b']);
    }

    #[Test]
    public function converts_entity_property_to_json(): void
    {
        $dql = 'SELECT HSTORE_TO_JSON(t.data) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsHstores t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame('1', $decoded['a']);
        $this->assertSame('2', $decoded['b']);
        $this->assertSame('3', $decoded['c']);
    }
}
