<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract;
use Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateTestCase;

class DateExtractTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return ['DATE_EXTRACT' => DateExtract::class];
    }

    public function test_extract_year(): void
    {
        $dql = "SELECT DATE_EXTRACT('year', t.date1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2023, $result[0]['result']);
    }

    public function test_extract_month(): void
    {
        $dql = "SELECT DATE_EXTRACT('month', t.date1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(6, $result[0]['result']);
    }

    public function test_extract_day(): void
    {
        $dql = "SELECT DATE_EXTRACT('day', t.date1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(15, $result[0]['result']);
    }

    public function test_extract_with_column_reference(): void
    {
        $dql = 'SELECT DATE_EXTRACT(\'year\', t.date1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2023, $result[0]['result']);
    }
}
