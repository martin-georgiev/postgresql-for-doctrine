<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeTime;
use PHPUnit\Framework\Attributes\Test;

class MakeTimeTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_TIME' => MakeTime::class,
        ];
    }

    #[Test]
    public function can_create_time_from_components(): void
    {
        $dql = 'SELECT MAKE_TIME(10, 30, 0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('10:30:00', $result[0]['result']);
    }
}

