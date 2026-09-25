<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\NthValue;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\Test;

final class NthValueTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NTH_VALUE' => NthValue::class,
            'OVER' => Over::class,
        ];
    }

    #[Test]
    public function returns_the_nth_value_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(NTH_VALUE(n.integer1, 1), ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_nth_value_from_entity_fields(): void
    {
        $dql = 'SELECT OVER(NTH_VALUE(n.integer1, n.id), PARTITION BY n.bigint1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
