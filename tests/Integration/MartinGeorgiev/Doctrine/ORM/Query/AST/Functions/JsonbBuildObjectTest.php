<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbBuildObject;
use PHPUnit\Framework\Attributes\Test;

class JsonbBuildObjectTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_BUILD_OBJECT' => JsonbBuildObject::class,
        ];
    }

    #[Test]
    public function can_build_jsonb_object_with_key_value_pairs(): void
    {
        $dql = "SELECT JSONB_BUILD_OBJECT('name', 'test', 'value', '123') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $decoded = \json_decode((string) $result[0]['result'], true);
        $this->assertSame('test', $decoded['name']);
        $this->assertSame('123', $decoded['value']);
    }

    #[Test]
    public function can_build_jsonb_object_with_multiple_pairs(): void
    {
        $dql = "SELECT JSONB_BUILD_OBJECT('a', '1', 'b', '2', 'c', '3') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $decoded = \json_decode((string) $result[0]['result'], true);
        $this->assertCount(3, $decoded);
        $this->assertSame('1', $decoded['a']);
        $this->assertSame('2', $decoded['b']);
        $this->assertSame('3', $decoded['c']);
    }
}
