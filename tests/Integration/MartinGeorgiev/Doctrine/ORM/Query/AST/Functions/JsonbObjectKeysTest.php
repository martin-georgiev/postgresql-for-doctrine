<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbObjectKeys;

class JsonbObjectKeysTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_OBJECT_KEYS' => JsonbObjectKeys::class,
        ];
    }

    public function test_jsonb_object_keys(): void
    {
        $dql = 'SELECT JSONB_OBJECT_KEYS(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }
}
