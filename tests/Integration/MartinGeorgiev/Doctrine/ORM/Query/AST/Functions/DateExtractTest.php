<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\DateExtract;
use PHPUnit\Framework\Attributes\Test;

final class DateExtractTest extends DateTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'DATE_EXTRACT' => DateExtract::class,
        ];
    }

    #[Test]
    public function returns_the_extracted_field_from_an_entity_field(): void
    {
        $dql = "SELECT DATE_EXTRACT('year', t.date1) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsDates t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('2023', $result[0]['result']);
    }
}
