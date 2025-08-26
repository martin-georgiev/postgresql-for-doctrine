<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract;
use PHPUnit\Framework\Attributes\Test;

class DateExtractTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_EXTRACT' => DateExtract::class,
        ];
    }

    #[Test]
    public function date_extract_with_year(): void
    {
        $dql = "SELECT DATE_EXTRACT('year', t.date1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023', $result[0]['result']);
    }

    #[Test]
    public function date_extract_with_month(): void
    {
        $dql = "SELECT DATE_EXTRACT('month', t.date1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('6', $result[0]['result']);
    }

    #[Test]
    public function date_extract_with_day(): void
    {
        $dql = "SELECT DATE_EXTRACT('day', t.date1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('15', $result[0]['result']);
    }

    #[Test]
    public function date_extract_with_column_reference(): void
    {
        $dql = "SELECT DATE_EXTRACT('year', t.date1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023', $result[0]['result']);
    }
}
