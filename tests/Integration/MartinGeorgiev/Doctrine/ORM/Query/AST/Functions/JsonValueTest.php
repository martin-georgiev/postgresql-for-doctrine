<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonValue;
use PHPUnit\Framework\Attributes\Test;

final class JsonValueTest extends JsonTestCase
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
    public function returns_the_scalar_value_at_a_path_from_an_entity_field(): void
    {
        $dql = "SELECT JSON_VALUE(t.jsonObject1, '$.name') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('John', $result[0]['result']);
    }
}
