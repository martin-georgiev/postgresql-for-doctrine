<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Int4range;
use PHPUnit\Framework\Attributes\Test;

class Int4rangeTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INT4RANGE' => Int4range::class,
        ];
    }

    #[Test]
    public function can_create_integer_range_with_default_bounds(): void
    {
        $dql = 'SELECT INT4RANGE(t.integer1, t.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[10,20)', $result[0]['result']);
    }

    #[Test]
    public function can_create_integer_range_with_custom_bounds(): void
    {
        $dql = "SELECT INT4RANGE(t.integer1, t.integer2, '(]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[11,21)', $result[0]['result']);
    }
}
