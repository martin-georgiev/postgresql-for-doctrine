<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate;
use PHPUnit\Framework\Attributes\Test;

class ToDateTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_DATE' => ToDate::class,
        ];
    }

    #[Test]
    public function can_convert_string_to_date(): void
    {
        $dql = "SELECT TO_DATE('05 Dec 2000', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2000-12-05', $result[0]['result']);
    }

    #[Test]
    public function throws_exception_for_invalid_date_input(): void
    {
        $this->expectException(DriverException::class);
        $dql = "SELECT TO_DATE('invalid_date', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function returns_default_date_for_invalid_format(): void
    {
        $dql = "SELECT TO_DATE('05 Dec 2000', 'invalid_format') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2005-01-01', $result[0]['result']);
    }

    #[Test]
    public function throws_exception_for_unsupported_format_type(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT TO_DATE('05 Dec 2000', 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function throws_exception_for_null_input(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT TO_DATE(null, 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }
}
