<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbTypeof;
use PHPUnit\Framework\Attributes\Test;

final class JsonbTypeofTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_TYPEOF' => JsonbTypeof::class,
        ];
    }

    #[Test]
    public function returns_the_jsonb_type_from_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_TYPEOF(t.jsonbObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('object', $result[0]['result']);
    }
}
