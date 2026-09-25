<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Lead;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Over;
use PHPUnit\Framework\Attributes\Test;

final class LeadTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LEAD' => Lead::class,
            'OVER' => Over::class,
        ];
    }

    #[Test]
    public function returns_the_following_value_from_a_numeric_literal(): void
    {
        $dql = 'SELECT OVER(LEAD(5), ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function returns_the_following_value_from_an_entity_field(): void
    {
        $dql = 'SELECT OVER(LEAD(n.integer1), PARTITION BY n.bigint1 ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function returns_the_following_value_with_an_offset(): void
    {
        $dql = 'SELECT OVER(LEAD(n.integer1, 0), ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(10, $result[0]['result']);
    }

    #[Test]
    public function returns_the_following_value_with_a_default(): void
    {
        $dql = 'SELECT OVER(LEAD(n.integer1, 1, 0), ORDER BY n.integer2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0, $result[0]['result']);
    }
}
