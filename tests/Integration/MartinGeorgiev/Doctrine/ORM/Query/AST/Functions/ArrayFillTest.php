<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayFill;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cast;
use PHPUnit\Framework\Attributes\Test;

class ArrayFillTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ARRAY_FILL' => ArrayFill::class,
            'ARRAY' => Arr::class,
            'CAST' => Cast::class,
        ];
    }

    #[Test]
    public function can_fill_array_with_integer_value(): void
    {
        $dql = "SELECT ARRAY_FILL(7, ARRAY('3')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertCount(3, $actual);
        $this->assertSame([7, 7, 7], $actual);
    }

    #[Test]
    public function can_fill_array_with_string_value(): void
    {
        $dql = "SELECT ARRAY_FILL(CAST('x' AS TEXT), ARRAY('3')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($actual);
        $this->assertCount(3, $actual);
        $this->assertSame(['x', 'x', 'x'], $actual);
    }

    #[Test]
    public function can_fill_array_with_boolean_value(): void
    {
        $dql = "SELECT ARRAY_FILL(CAST('true' AS BOOLEAN), ARRAY('2')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('{t,t}', $result[0]['result']);
    }

    #[Test]
    public function can_fill_multi_dimensional_array(): void
    {
        $dql = "SELECT ARRAY_FILL(11, ARRAY('2', '3')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('{{11,11,11},{11,11,11}}', $result[0]['result']);
    }

    #[Test]
    public function can_fill_array_with_custom_lower_bounds(): void
    {
        $dql = "SELECT ARRAY_FILL(7, ARRAY('3'), ARRAY('2')) as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertSame('[2:4]={7,7,7}', $result[0]['result']);
    }
}
