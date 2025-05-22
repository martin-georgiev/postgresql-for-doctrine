<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Regexp;

class RegexpTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP' => Regexp::class,
        ];
    }

    public function test_regexp(): void
    {
        // NOTE: Using a string literal for the first argument because DQL does not support ->> operator in this context.
        $dql = "SELECT REGEXP('John', 'J.*n') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertIsArray($result);
        $this->assertTrue((bool) $result[0]['result']);
    }
}
