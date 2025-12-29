<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetObject;
use PHPUnit\Framework\Attributes\Test;

class JsonGetObjectTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_GET_OBJECT' => JsonGetObject::class,
        ];
    }

    #[Test]
    public function can_get_nested_object_by_path(): void
    {
        $dql = "SELECT JSON_GET_OBJECT(t.jsonObject1, '{address}') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $decoded = \json_decode((string) $result[0]['result'], true);
        $this->assertSame('New York', $decoded['city']);
    }

    #[Test]
    public function can_get_deeply_nested_value(): void
    {
        $dql = "SELECT JSON_GET_OBJECT(t.jsonObject1, '{address,city}') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"New York"', $result[0]['result']);
    }
}
