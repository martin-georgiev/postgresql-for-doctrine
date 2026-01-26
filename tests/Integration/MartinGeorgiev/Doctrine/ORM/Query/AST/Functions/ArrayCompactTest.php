<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCompact;
use PHPUnit\Framework\Attributes\Test;

class ArrayCompactTest extends ArrayTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'array_compact function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_COMPACT' => ArrayCompact::class,
        ];
    }

    #[Test]
    public function can_compact_array_with_nulls(): void
    {
        $dql = "SELECT ARRAY_COMPACT(ARRAY['a', null, 'b', null, 'c']) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame(['a', 'b', 'c'], $actual);
    }
}
