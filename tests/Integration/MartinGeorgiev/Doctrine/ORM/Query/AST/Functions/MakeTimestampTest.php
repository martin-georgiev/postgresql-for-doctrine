<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamp;
use PHPUnit\Framework\Attributes\Test;

class MakeTimestampTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_TIMESTAMP' => MakeTimestamp::class,
        ];
    }

    #[Test]
    public function can_create_timestamp_from_components(): void
    {
        $dql = 'SELECT MAKE_TIMESTAMP(2023, 6, 15, 10, 30, 0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('2023-06-15 10:30:00', $result[0]['result']);
    }
}

