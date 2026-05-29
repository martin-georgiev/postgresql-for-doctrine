<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ClockTimestamp;
use PHPUnit\Framework\Attributes\Test;

class ClockTimestampTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CLOCK_TIMESTAMP' => ClockTimestamp::class,
        ];
    }

    #[Test]
    public function returns_non_null_timestamp_string(): void
    {
        $dql = 'SELECT CLOCK_TIMESTAMP() as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }
}
