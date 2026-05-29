<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\HstoreToJsonLoose;
use PHPUnit\Framework\Attributes\Test;

class HstoreToJsonLooseTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_TO_JSON_LOOSE' => HstoreToJsonLoose::class,
        ];
    }

    #[Test]
    public function converts_hstore_literal_to_json_with_loose_typing(): void
    {
        $dql = "SELECT HSTORE_TO_JSON_LOOSE('\"a\"=>\"1\",\"b\"=>\"2\"') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame(1, $decoded['a']);
        $this->assertSame(2, $decoded['b']);
    }

    #[Test]
    public function converts_entity_property_to_json_with_loose_typing(): void
    {
        $dql = 'SELECT HSTORE_TO_JSON_LOOSE(t.data) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsHstores t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame(1, $decoded['a']);
        $this->assertSame(2, $decoded['b']);
        $this->assertSame(3, $decoded['c']);
    }
}
