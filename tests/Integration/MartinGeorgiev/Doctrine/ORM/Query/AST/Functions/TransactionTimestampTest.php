<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TransactionTimestamp;
use PHPUnit\Framework\Attributes\Test;

class TransactionTimestampTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'TRANSACTION_TIMESTAMP' => TransactionTimestamp::class,
        ];
    }

    #[Test]
    public function returns_non_null_timestamp_string(): void
    {
        $dql = 'SELECT TRANSACTION_TIMESTAMP() as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
    }
}
