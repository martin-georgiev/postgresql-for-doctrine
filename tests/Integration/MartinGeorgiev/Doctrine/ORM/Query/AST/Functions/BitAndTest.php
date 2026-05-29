<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BitAnd;
use PHPUnit\Framework\Attributes\Test;

class BitAndTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'BIT_AND' => BitAnd::class,
        ];
    }

    #[Test]
    public function aggregates_integer_field_with_bitwise_and(): void
    {
        $dql = 'SELECT BIT_AND(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function aggregates_second_integer_field_with_bitwise_and(): void
    {
        $dql = 'SELECT BIT_AND(t.integer2) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(20, $result[0]['result']);
    }
}
