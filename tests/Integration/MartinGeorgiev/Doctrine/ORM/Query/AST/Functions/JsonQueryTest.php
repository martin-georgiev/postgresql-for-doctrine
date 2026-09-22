<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonQuery;
use PHPUnit\Framework\Attributes\Test;

final class JsonQueryTest extends JsonTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'JSON_QUERY function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSON_QUERY' => JsonQuery::class,
        ];
    }

    #[Test]
    public function returns_the_queried_json_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_QUERY(t.jsonObject1, '$.address') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame('New York', $decoded['city']);
    }
}
