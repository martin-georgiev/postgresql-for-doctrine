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
        $this->assertEquals(2023, $result[0]['result']);
    }

    #[Test]
    public function can_extract_month(): void
    {
        $dql = "SELECT DATE_PART('month', t.datetime1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(6, $result[0]['result']);
    }

    #[Test]
    public function can_extract_day(): void
    {
        $dql = "SELECT DATE_PART('day', t.datetime1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(15, $result[0]['result']);
    }

    #[Test]
    public function can_extract_hour(): void
    {
        $dql = "SELECT DATE_PART('hour', t.datetime1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(10, $result[0]['result']);
    }

    #[Test]
    public function can_extract_minute(): void
    {
        $dql = "SELECT DATE_PART('minute', t.datetime1) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(30, $result[0]['result']);
    }
}
