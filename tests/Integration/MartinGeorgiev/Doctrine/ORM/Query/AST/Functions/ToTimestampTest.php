<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;

class ToTimestampTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'to_timestamp' => ToTimestamp::class,
        ];
    }

    public function test_totimestamp(): void
    {
        $dql = "SELECT to_timestamp('05 Dec 2000 at 11:55 and 32 seconds', 'DD Mon YYYY tt HH24:MI ttt SS ttttttt') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('2000-12-05 11:55:32+00', $result[0]['result']);
    }

    public function test_totimestamp_with_invalid_input(): void
    {
        $this->expectException(DriverException::class);
        $dql = "SELECT to_timestamp('invalid_date', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    public function test_totimestamp_with_invalid_format(): void
    {
        $dql = "SELECT to_timestamp('05 Dec 2000', 'invalid_format') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        static::assertSame('2005-01-01 00:00:00+00', $result[0]['result']);
    }

    public function test_totimestamp_with_wrong_type_format(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_timestamp('05 Dec 2000', 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    public function test_totimestamp_with_wrong_type_input(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_timestamp(null, 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }
}
