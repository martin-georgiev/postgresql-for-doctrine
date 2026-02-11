<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue;
use PHPUnit\Framework\Attributes\Test;

class JsonValueTest extends JsonTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'JSON_VALUE function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSON_VALUE' => JsonValue::class,
        ];
    }

    #[Test]
    public function can_extract_scalar_value(): void
    {
        $dql = "SELECT JSON_VALUE(t.jsonObject1, '$.name') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('John', $result[0]['result']);
    }

    #[Test]
    public function can_extract_nested_value(): void
    {
        $dql = "SELECT JSON_VALUE(t.jsonObject1, '$.address.city') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('New York', $result[0]['result']);
    }

    #[Test]
    public function can_extract_numeric_value(): void
    {
        $dql = "SELECT JSON_VALUE(t.jsonObject1, '$.age') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('30', $result[0]['result']);
    }
}
