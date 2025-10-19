<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsrange;
use PHPUnit\Framework\Attributes\Test;

class TsrangeTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TSRANGE' => Tsrange::class,
        ];
    }

    #[Test]
    public function can_create_timestamp_range_with_default_bounds(): void
    {
        $dql = 'SELECT TSRANGE(t.datetime1, t.datetime2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('["2023-06-15 10:30:00","2023-06-16 11:45:00")', $result[0]['result']);
    }

    #[Test]
    public function can_create_timestamp_range_with_custom_bounds(): void
    {
        $dql = "SELECT TSRANGE(t.datetime1, t.datetime2, '(]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('("2023-06-15 10:30:00","2023-06-16 11:45:00"]', $result[0]['result']);
    }
}
