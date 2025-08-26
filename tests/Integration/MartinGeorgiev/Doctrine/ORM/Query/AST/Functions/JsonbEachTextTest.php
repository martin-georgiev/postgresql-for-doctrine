<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbEachText;
use PHPUnit\Framework\Attributes\Test;

class JsonbEachTextTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EACH_TEXT' => JsonbEachText::class,
        ];
    }

    #[Test]
    public function jsonb_each_text(): void
    {
        $dql = 'SELECT JSONB_EACH_TEXT(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
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

    #[Test]
    public function jsonb_each_text_with_empty_object(): void
    {
        $dql = 'SELECT JSONB_EACH_TEXT(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 4';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(0, $result);
    }

    #[Test]
    public function jsonb_each_text_with_different_object(): void
    {
        $dql = 'SELECT JSONB_EACH_TEXT(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 2';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);
    }

    #[Test]
    public function jsonb_each_text_with_nulls(): void
    {
        $dql = 'SELECT JSONB_EACH_TEXT(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 5';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);
    }

    #[Test]
    public function jsonb_each_text_with_empty_tags_array(): void
    {
        $dql = 'SELECT JSONB_EACH_TEXT(t.object1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 3';
        $result = $this->executeDqlQuery($dql);
        $this->assertCount(4, $result);
    }
}
