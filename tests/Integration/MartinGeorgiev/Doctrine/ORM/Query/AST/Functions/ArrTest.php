<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;

class ArrTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return ['ARR' => Arr::class];
    }

    public function test_arr_with_text_values(): void
    {
        $dql = "SELECT ARR('apple', 'banana', 'orange') as result";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals(['apple', 'banana', 'orange'], $actual);
    }

    public function test_arr_with_integer_values(): void
    {
        $dql = 'SELECT ARR(1, 2, 3) as result';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([1, 2, 3], $actual);
    }

    public function test_arr_with_boolean_values(): void
    {
        $dql = 'SELECT ARR(true, false, true) as result';

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals([true, false, true], $actual);
    }

    public function test_arr_with_mixed_values(): void
    {
        $dql = "SELECT ARR('apple', 1, true) as result";

        $result = $this->executeDqlQuery($dql);
        $actual = $this->transformPostgresArray($result[0]['result']);
        $this->assertEquals(['apple', 1, true], $actual);
    }
}
