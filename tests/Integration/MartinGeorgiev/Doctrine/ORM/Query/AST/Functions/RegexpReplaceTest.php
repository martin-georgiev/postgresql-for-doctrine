<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RegexpReplace;
use PHPUnit\Framework\Attributes\Test;

class RegexpReplaceTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'REGEXP_REPLACE' => RegexpReplace::class,
        ];
    }

    #[Test]
    public function regexp_replace(): void
    {
        // NOTE: Using string literals for arguments due to DQL limitations with field extraction.
        $dql = "SELECT REGEXP_REPLACE('John', 'J.*n', 'Jane') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('Jane', $result[0]['result']);
    }

    #[Test]
    public function regexp_replace_no_match(): void
    {
        $dql = "SELECT REGEXP_REPLACE('John', 'Jane', 'Jane') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('John', $result[0]['result']);
    }
}
