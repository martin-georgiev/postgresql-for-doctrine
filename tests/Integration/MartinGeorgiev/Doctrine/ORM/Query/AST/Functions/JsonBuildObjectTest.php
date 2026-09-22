<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonBuildObject;
use PHPUnit\Framework\Attributes\Test;

final class JsonBuildObjectTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_BUILD_OBJECT' => JsonBuildObject::class,
        ];
    }

    #[Test]
    public function creates_a_json_object_from_text_literals(): void
    {
        $dql = "SELECT JSON_BUILD_OBJECT('name', 'test', 'value', '123') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame('test', $decoded['name']);
        $this->assertSame('123', $decoded['value']);
    }

    #[Test]
    public function creates_a_json_object_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_BUILD_OBJECT('id', t.id) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{"id" : 1}', $result[0]['result']);
    }
}
