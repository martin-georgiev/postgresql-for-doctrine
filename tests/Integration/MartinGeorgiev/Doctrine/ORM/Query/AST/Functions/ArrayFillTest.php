<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayFill;
use PHPUnit\Framework\Attributes\Test;

class ArrayFillTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_FILL' => ArrayFill::class,
        ];
    }

    #[Test]
    public function can_fill_array_with_value(): void
    {
        $dql = "SELECT ARRAY_FILL(7, ARRAY[3]) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([7, 7, 7], $actual);
    }
}
