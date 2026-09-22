<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayAgg;
use PHPUnit\Framework\Attributes\Test;

final class ArrayAggTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_AGG' => ArrayAgg::class,
        ];
    }

    #[Test]
    public function returns_the_aggregated_arrays_from_an_entity_field(): void
    {
        $dql = 'SELECT ARRAY_AGG(t.textArray) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('{{apple,banana,orange}}', $result[0]['result']);
    }
}
