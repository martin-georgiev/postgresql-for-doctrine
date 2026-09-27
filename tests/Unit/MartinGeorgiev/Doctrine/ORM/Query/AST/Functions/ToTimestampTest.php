<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTimestamp;
use PHPUnit\Framework\Attributes\Test;

final class ToTimestampTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TO_TIMESTAMP' => ToTimestamp::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'converts text to timestamp using format pattern' => "SELECT to_timestamp(c0_.text1, 'DD Mon YYYY') AS sclr_0 FROM ContainsTexts c0_",
            'converts a Unix epoch to timestamp' => 'SELECT to_timestamp(1700000000) AS sclr_0 FROM ContainsTexts c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'converts text to timestamp using format pattern' => \sprintf("SELECT TO_TIMESTAMP(e.text1, 'DD Mon YYYY') FROM %s e", ContainsTexts::class),
            'converts a Unix epoch to timestamp' => \sprintf('SELECT TO_TIMESTAMP(1700000000) FROM %s e', ContainsTexts::class),
        ];
    }

    #[Test]
    public function throws_exception_for_missing_arguments(): void
    {
        $this->expectException(InvalidArgumentForVariadicFunctionException::class);
        $this->expectExceptionMessage('to_timestamp() requires at least 1 argument');

        $dql = \sprintf('SELECT TO_TIMESTAMP() FROM %s e', ContainsTexts::class);
        $this->buildEntityManager()->createQuery($dql)->getSQL();
    }
}
