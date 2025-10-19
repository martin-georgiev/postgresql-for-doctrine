<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\DBAL\Exception\DriverException;
use Doctrine\ORM\Query\QueryException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;
use PHPUnit\Framework\Attributes\Test;

class ToTimestampTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'to_timestamp' => ToTimestamp::class,
        ];
    }

    #[Test]
    public function totimestamp(): void
    {
        $dql = "SELECT to_timestamp('05 Dec 2000 at 11:55 and 32 seconds', 'DD Mon YYYY tt HH24:MI ttt SS ttttttt') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2000-12-05 11:55:32+00', $result[0]['result']);
    }

    #[Test]
    public function totimestamp_throws_with_invalid_input(): void
    {
        $this->expectException(DriverException::class);
        $dql = "SELECT to_timestamp('invalid_date', 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function totimestamp_with_invalid_format(): void
    {
        $dql = "SELECT to_timestamp('05 Dec 2000', 'invalid_format') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2005-01-01 00:00:00+00', $result[0]['result']);
    }

    #[Test]
    public function totimestamp_throws_with_unsupported_format_type(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_timestamp('05 Dec 2000', 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function totimestamp_throws_with_unsupported_null_input(): void
    {
        $this->expectException(QueryException::class);
        $dql = "SELECT to_timestamp(null, 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }
}
