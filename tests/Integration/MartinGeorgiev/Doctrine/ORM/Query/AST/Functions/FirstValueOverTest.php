<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\FirstValueOver;
use PHPUnit\Framework\Attributes\Test;

final class FirstValueOverTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'FIRST_VALUE_OVER' => FirstValueOver::class,
        ];
    }

    #[Test]
    public function returns_the_first_value_from_a_numeric_literal(): void
    {
        $dql = 'SELECT FIRST_VALUE_OVER(5, ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(5, $result[0]['result']);
    }

    #[Test]
    public function returns_the_first_value_from_entity_fields(): void
    {
        $dql = 'SELECT FIRST_VALUE_OVER(n.integer1, PARTITION BY n.integer1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }
}
