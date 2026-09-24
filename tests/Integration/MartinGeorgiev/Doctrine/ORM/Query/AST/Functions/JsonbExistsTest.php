<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonbExists;
use PHPUnit\Framework\Attributes\Test;

final class JsonbExistsTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSONB_EXISTS' => JsonbExists::class,
        ];
    }

    #[Test]
    public function returns_whether_the_key_exists_from_an_entity_field(): void
    {
        $dql = 'SELECT JSONB_EXISTS(t.jsonbObject1, :key) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql, ['key' => 'name']);
        $this->assertTrue($result[0]['result']);
    }
}
