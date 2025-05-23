<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToNumber;

class ToNumberTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'to_number' => ToNumber::class,
        ];
    }

    public function test_tonumber(): void
    {
        $dql = "SELECT to_number('12,454.8-', '99G999D9S') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('-12454.8', $result[0]['result']);
    }
}
