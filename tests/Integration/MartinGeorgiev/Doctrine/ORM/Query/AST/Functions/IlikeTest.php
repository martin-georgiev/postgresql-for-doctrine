<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ilike;

class IlikeTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ILIKE' => Ilike::class,
        ];
    }

    public function test_ilike(): void
    {
        // NOTE: Using string literals for arguments due to DQL limitations with field extraction.
        $dql = "SELECT ILIKE('John', 'john') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertTrue((bool) $result[0]['result']);
    }
}
