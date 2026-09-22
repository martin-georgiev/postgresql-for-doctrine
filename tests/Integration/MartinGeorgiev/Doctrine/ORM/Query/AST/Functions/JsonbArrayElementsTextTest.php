<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbArrayElementsText;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonGetField;
use PHPUnit\Framework\Attributes\Test;

final class JsonbArrayElementsTextTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_ARRAY_ELEMENTS_TEXT' => JsonbArrayElementsText::class,
            'JSON_GET_FIELD' => JsonGetField::class,
        ];
    }

    #[Test]
    public function returns_the_array_elements_as_text_from_an_entity_field(): void
    {
        $dql = "SELECT JSONB_ARRAY_ELEMENTS_TEXT(JSON_GET_FIELD(t.jsonbObject1, 'tags')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(2, $result);

        $values = \array_map(static fn (array $row): mixed => $row['result'], $result);
        $this->assertContains('developer', $values);
        $this->assertContains('manager', $values);
    }
}
