<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElements;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use PHPUnit\Framework\Attributes\Test;

final class JsonbArrayElementsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_ARRAY_ELEMENTS' => JsonbArrayElements::class,
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    #[Test]
    public function returns_the_array_elements_from_an_entity_field(): void
    {
        $dql = "SELECT JSONB_ARRAY_ELEMENTS(JSON_GET_FIELD(t.jsonbObject1, 'tags')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(2, $result);

        $values = [];
        foreach ($result as $row) {
            $this->assertIsString($row['result']);
            $values[] = \json_decode($row['result'], true);
        }

        $this->assertContains('developer', $values);
        $this->assertContains('manager', $values);
    }
}
