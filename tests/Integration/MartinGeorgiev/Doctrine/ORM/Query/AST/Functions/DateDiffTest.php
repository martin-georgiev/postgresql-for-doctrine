<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateDiff;
use PHPUnit\Framework\Attributes\Test;

class DateDiffTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_DIFF' => DateDiff::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(160000, 'DATE_DIFF function');
    }

    #[Test]
    public function returns_number_of_days_between_timestamps(): void
    {
        $dql = "SELECT DATE_DIFF('day', t.datetime1, t.datetime2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(1, $result[0]['result']);
    }

    #[Test]
    public function returns_number_of_hours_between_timestamps(): void
    {
        $dql = "SELECT DATE_DIFF('hour', t.datetime1, t.datetime2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame(25, $result[0]['result']);
    }
}
