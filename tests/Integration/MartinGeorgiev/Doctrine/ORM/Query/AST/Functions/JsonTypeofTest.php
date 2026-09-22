<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonTypeof;
use PHPUnit\Framework\Attributes\Test;

final class JsonTypeofTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'JSON_TYPEOF' => JsonTypeof::class,
        ];
    }

    #[Test]
    public function returns_the_json_type_from_an_entity_field(): void
    {
        $dql = 'SELECT JSON_TYPEOF(t.jsonObject1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('object', $result[0]['result']);
    }
}
