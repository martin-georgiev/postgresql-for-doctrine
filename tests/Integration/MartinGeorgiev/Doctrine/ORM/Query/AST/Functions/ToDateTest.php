<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToDate;
use PHPUnit\Framework\Attributes\Test;

final class ToDateTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_DATE' => ToDate::class,
        ];
    }

    #[Test]
    public function converts_a_literal_to_a_date(): void
    {
        $dql = "SELECT TO_DATE('05 Dec 2000', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2000-12-05', $result[0]['result']);
    }

    #[Test]
    public function rejects_a_numeric_format_argument(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT TO_DATE('05 Dec 2000', 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function rejects_a_null_argument(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT TO_DATE(null, 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }
}
