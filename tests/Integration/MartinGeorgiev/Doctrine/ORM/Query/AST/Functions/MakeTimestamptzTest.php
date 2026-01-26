<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTimestamptz;
use PHPUnit\Framework\Attributes\Test;

class MakeTimestamptzTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_TIMESTAMPTZ' => MakeTimestamptz::class,
        ];
    }

    #[Test]
    public function can_create_timestamptz_from_components(): void
    {
        $dql = 'SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2023-06-15 10:30:00', $result[0]['result']);
    }

    #[Test]
    public function can_create_timestamptz_with_timezone(): void
    {
        $dql = "SELECT MAKE_TIMESTAMPTZ(2023, 6, 15, 10, 30, 0, 'UTC') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('2023-06-15 10:30:00+00', $result[0]['result']);
    }
}

