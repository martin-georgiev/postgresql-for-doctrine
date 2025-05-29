<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate;

class ToDateTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'to_date' => ToDate::class,
        ];
    }

    public function test_todate(): void
    {
        $dql = "SELECT to_date('05 Dec 2000', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('2000-12-05', $result[0]['result']);
    }

    public function test_todate_throws_with_invalid_input(): void
    {
        $this->expectException(DriverException::class);
        $dql = "SELECT to_date('invalid_date', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    public function test_todate_with_invalid_format(): void
    {
        $dql = "SELECT to_date('05 Dec 2000', 'invalid_format') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('2005-01-01', $result[0]['result']);
    }

    public function test_todate_throws_with_unsupported_format_type(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_date('05 Dec 2000', 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    public function test_todate_throws_with_unsupported_null_input(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_date(null, 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }
}
