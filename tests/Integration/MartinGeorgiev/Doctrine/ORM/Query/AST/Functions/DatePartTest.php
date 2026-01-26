<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DatePart;
use PHPUnit\Framework\Attributes\Test;

class DatePartTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_PART' => DatePart::class,
        ];
    }

    #[Test]
    public function can_extract_year(): void
    {
        $dql = "SELECT DATE_PART('year', t.datetime1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023', $result[0]['result']);
    }
}

