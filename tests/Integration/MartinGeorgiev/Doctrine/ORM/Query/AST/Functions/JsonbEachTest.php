<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEach;

class JsonbEachTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EACH' => JsonbEach::class,
        ];
    }

    public function test_jsonb_each(): void
    {
        $dql = 'SELECT JSONB_EACH(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        foreach ($result as $row) {
            $this->assertIsArray($row);
            $this->assertArrayHasKey('result', $row);
            $this->assertIsString($row['result']);
            $decoded = \json_decode($row['result'], true);
            if (\is_array($decoded) && isset($decoded['key'], $decoded['value'])) {
                $key = $decoded['key'];
                $value = $decoded['value'];
            } else {
                $parts = \explode(':', \trim($row['result'], '{}"'));
                $key = $parts[0] ?? null;
                $value = $parts[1] ?? null;
            }

            $this->assertNotNull($key);
        }
    }

    public function test_jsonb_each_with_empty_object(): void
    {
        $dql = 'SELECT JSONB_EACH(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 4';
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
}
