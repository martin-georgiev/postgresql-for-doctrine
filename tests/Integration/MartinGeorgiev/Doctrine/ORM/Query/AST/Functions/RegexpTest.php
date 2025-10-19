<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Regexp;
use PHPUnit\Framework\Attributes\Test;

class RegexpTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP' => Regexp::class,
        ];
    }

    #[Test]
    public function regexp(): void
    {
        // NOTE: Using a string literal for the first argument because DQL does not support ->> operator in this context.
        $dql = "SELECT REGEXP('John', 'J.*n') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function regexp_negative(): void
    {
        $dql = "SELECT REGEXP('John', 'Jane') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
