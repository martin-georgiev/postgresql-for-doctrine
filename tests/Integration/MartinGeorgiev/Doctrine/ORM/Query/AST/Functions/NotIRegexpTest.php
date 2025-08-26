<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NotIRegexp;
use PHPUnit\Framework\Attributes\Test;

class NotIRegexpTest extends JsonTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NOT_IREGEXP' => NotIRegexp::class,
        ];
    }

    #[Test]
    public function not_iregexp(): void
    {
        // NOTE: Using string literals for arguments due to DQL limitations with field extraction.
        $dql = "SELECT NOT_IREGEXP('John', 'jane') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function not_iregexp_negative(): void
    {
        $dql = "SELECT NOT_IREGEXP('John', 'John') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertFalse($result[0]['result']);
    }
}
