<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\ParserException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;
use PHPUnit\Framework\Attributes\Test;

final class ToTimestampTest extends TextTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TIMESTAMP' => ToTimestamp::class,
        ];
    }

    #[Test]
    public function converts_the_text_to_a_timestamp_from_a_literal(): void
    {
        $dql = "SELECT TO_TIMESTAMP('05 Dec 2000 at 11:55 and 32 seconds', 'DD Mon YYYY tt HH24:MI ttt SS ttttttt') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2000-12-05 11:55:32+00', $result[0]['result']);
    }

    #[Test]
    public function rejects_a_numeric_format_argument(): void
    {
        $this->expectException(ParserException::class);
        $dql = "SELECT TO_TIMESTAMP('05 Dec 2000', 1) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function rejects_a_null_argument(): void
    {
        $this->expectException(ParserException::class);
        $dql = "SELECT TO_TIMESTAMP(NULL, 'DD Mon YYYY') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1";
        $this->executeDqlQuery($dql);
    }

    #[Test]
    public function converts_the_epoch_to_a_timestamp_from_a_literal(): void
    {
        $dql = 'SELECT TO_TIMESTAMP(0) as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsTexts t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1970-01-01 00:00:00+00', $result[0]['result']);
    }
}
