<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPositions;
use PHPUnit\Framework\Attributes\Test;

class ArrayPositionsTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_POSITIONS' => ArrayPositions::class,
        ];
    }

    #[Test]
    public function array_positions_with_text_array(): void
    {
        $dql = 'SELECT ARRAY_POSITIONS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql, ['value' => 'apple']);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([2], $actual);
    }

    #[Test]
    public function array_positions_with_integer_array(): void
    {
        $dql = 'SELECT ARRAY_POSITIONS(t.integerArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql, ['value' => 1]);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([2], $actual);
    }

    #[Test]
    public function array_positions_with_boolean_array(): void
    {
        $dql = 'SELECT ARRAY_POSITIONS(t.boolArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql, ['value' => true]);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([2], $actual);
    }

    #[Test]
    public function array_positions_with_not_found(): void
    {
        $dql = 'SELECT ARRAY_POSITIONS(t.textArray, :value) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays t 
                WHERE t.id = 2';

        $result = $this->executeDqlQuery($dql, ['value' => 'mango']);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertSame([], $actual);
    }
}
