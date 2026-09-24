<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy;
use PHPUnit\Framework\Attributes\Test;

final class IsContainedByTest extends ArrayTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'IS_CONTAINED_BY' => IsContainedBy::class,
            'ARR' => Arr::class,
        ];
    }

    #[Test]
    public function returns_whether_the_array_is_contained_from_a_literal_operand(): void
    {
        $dql = "SELECT IS_CONTAINED_BY(ARR('apple'), t.textArray) as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsArrays t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
