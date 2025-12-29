<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ReturnsValueForJsonValue;
use PHPUnit\Framework\Attributes\Test;

class ReturnsValueForJsonValueTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RETURNS_VALUE_FOR_JSON_VALUE' => ReturnsValueForJsonValue::class,
        ];
    }

    #[Test]
    public function returns_true_when_path_exists(): void
    {
        $dql = "SELECT RETURNS_VALUE_FOR_JSON_VALUE(t.jsonbObject1, '$.name') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_false_when_path_does_not_exist(): void
    {
        $dql = "SELECT RETURNS_VALUE_FOR_JSON_VALUE(t.jsonbObject1, '$.nonexistent') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }

    #[Test]
    public function can_check_nested_path(): void
    {
        $dql = "SELECT RETURNS_VALUE_FOR_JSON_VALUE(t.jsonbObject1, '$.address.city') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
