<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonSerialize;
use PHPUnit\Framework\Attributes\Test;

class JsonSerializeTest extends JsonTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'JSON_SERIALIZE function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSON_SERIALIZE' => JsonSerialize::class,
        ];
    }

    #[Test]
    public function can_serialize_json_to_text(): void
    {
        $dql = 'SELECT JSON_SERIALIZE(t.jsonObject1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $decoded = \json_decode((string) $result[0]['result'], true);
        $this->assertSame('John', $decoded['name']);
        $this->assertSame(30, $decoded['age']);
    }
}
