<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace;

class RegexpReplaceTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_REPLACE' => RegexpReplace::class,
        ];
    }

    public function test_regexp_replace(): void
    {
        // NOTE: Using string literals for arguments due to DQL limitations with field extraction.
        $dql = "SELECT REGEXP_REPLACE('John', 'J.*n', 'Jane') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertSame('Jane', $result[0]['result']);
    }
}
