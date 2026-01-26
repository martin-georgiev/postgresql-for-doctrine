<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\MakeDate;
use PHPUnit\Framework\Attributes\Test;

class MakeDateTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'MAKE_DATE' => MakeDate::class,
        ];
    }

    #[Test]
    public function can_create_date_from_components(): void
    {
        $dql = 'SELECT MAKE_DATE(2023, 6, 15) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('2023-06-15', $result[0]['result']);
    }
}

