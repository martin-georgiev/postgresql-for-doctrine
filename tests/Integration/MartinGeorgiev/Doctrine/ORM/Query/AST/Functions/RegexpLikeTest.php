<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpLike;

class RegexpLikeTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_LIKE' => RegexpLike::class,
        ];
    }

    public function test_regexp_like(): void
    {
        // NOTE: Using string literals for arguments due to DQL limitations with field extraction.
        $dql = "SELECT REGEXP_LIKE('John', 'J.*n') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertTrue((bool) $result[0]['result']);
    }
}
