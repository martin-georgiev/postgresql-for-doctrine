<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject;
use PHPUnit\Framework\Attributes\Test;

final class JsonGetObjectTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_OBJECT' => JsonGetObject::class,
        ];
    }

    #[Test]
    public function returns_the_object_at_a_path_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_GET_OBJECT(t.jsonObject1, '{address}') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $decoded = \json_decode($result[0]['result'], true);
        $this->assertIsArray($decoded);
        $this->assertSame('New York', $decoded['city']);
    }
}
