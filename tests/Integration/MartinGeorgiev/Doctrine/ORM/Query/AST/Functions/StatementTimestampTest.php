<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StatementTimestamp;
use PHPUnit\Framework\Attributes\Test;

class StatementTimestampTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STATEMENT_TIMESTAMP' => StatementTimestamp::class,
        ];
    }

    #[Test]
    public function returns_non_null_timestamp_string(): void
    {
        $dql = 'SELECT STATEMENT_TIMESTAMP() as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }
}
