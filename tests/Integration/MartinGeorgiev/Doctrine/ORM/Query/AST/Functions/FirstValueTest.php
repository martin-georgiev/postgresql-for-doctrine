<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FirstValue;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\Test;

final class FirstValueTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FIRST_VALUE' => FirstValue::class,
            'OVER' => Over::class,
        ];
    }

    #[Test]
    public function returns_the_first_value_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(FIRST_VALUE(5), ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_first_value_from_an_entity_field(): void
    {
        $dql = 'SELECT OVER(FIRST_VALUE(n.integer1), PARTITION BY n.bigint1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
