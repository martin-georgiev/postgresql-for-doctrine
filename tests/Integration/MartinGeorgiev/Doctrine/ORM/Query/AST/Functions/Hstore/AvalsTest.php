<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Avals;
use PHPUnit\Framework\Attributes\Test;

class AvalsTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_AVALS' => Avals::class,
        ];
    }

    #[Test]
    public function can_return_values_as_array_from_hstore_literal(): void
    {
        $dql = "SELECT HSTORE_AVALS('\"a\"=>\"1\",\"b\"=>\"2\"') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $values = $this->transformPostgresArray($result[0]['result']);
        $this->assertEqualsCanonicalizing(['1', '2'], $values);
    }

    #[Test]
    public function can_return_values_as_array_from_entity_property(): void
    {
        $dql = 'SELECT HSTORE_AVALS(t.data) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsHstores t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $values = $this->transformPostgresArray($result[0]['result']);
        $this->assertEqualsCanonicalizing(['1', '2', '3'], $values);
    }
}
