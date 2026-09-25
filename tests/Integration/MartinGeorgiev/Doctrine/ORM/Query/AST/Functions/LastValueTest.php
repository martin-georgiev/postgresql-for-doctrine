<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\LastValue;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\Test;

final class LastValueTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LAST_VALUE' => LastValue::class,
            'OVER' => Over::class,
        ];
    }

    #[Test]
    public function returns_the_last_value_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(LAST_VALUE(5), ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_last_value_from_an_entity_field(): void
    {
        $dql = 'SELECT OVER(LAST_VALUE(n.integer1), PARTITION BY n.bigint1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
