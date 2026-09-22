<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateBin;
use PHPUnit\Framework\Attributes\Test;

final class DateBinTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_BIN' => DateBin::class,
        ];
    }

    #[Test]
    public function returns_the_binned_timestamp_from_an_entity_field(): void
    {
        $dql = "SELECT DATE_BIN('15 minutes', t.datetime1, '2023-06-15 00:00:00') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023-06-15 10:30:00', $result[0]['result']);
    }
}
