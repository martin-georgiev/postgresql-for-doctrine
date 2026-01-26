<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Age;
use PHPUnit\Framework\Attributes\Test;

class AgeTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'AGE' => Age::class,
        ];
    }

    #[Test]
    public function can_calculate_age_between_timestamps(): void
    {
        $dql = 'SELECT AGE(t.datetime2, t.datetime1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringContainsString('1 day', $result[0]['result']);
        $this->assertStringContainsString('01:15:00', $result[0]['result']);
    }
}

