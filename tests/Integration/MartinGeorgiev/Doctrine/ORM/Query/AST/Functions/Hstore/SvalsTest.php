<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Svals;
use PHPUnit\Framework\Attributes\Test;

class SvalsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_SVALS' => Svals::class,
        ];
    }

    #[Test]
    public function can_return_values_as_set_from_hstore_literal(): void
    {
        $dql = "SELECT HSTORE_SVALS('\"a\"=>\"1\",\"b\"=>\"2\"') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $values = \array_column($result, 'result');
        $this->assertEqualsCanonicalizing(['1', '2'], $values);
    }

    #[Test]
    public function can_return_values_as_set_from_entity_property(): void
    {
        $dql = 'SELECT HSTORE_SVALS(t.data) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsHstores t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $values = \array_column($result, 'result');
        $this->assertEqualsCanonicalizing(['1', '2', '3'], $values);
    }
}
