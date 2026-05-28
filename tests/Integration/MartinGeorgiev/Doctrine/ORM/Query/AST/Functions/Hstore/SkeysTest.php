<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Skeys;
use PHPUnit\Framework\Attributes\Test;

class SkeysTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_SKEYS' => Skeys::class,
        ];
    }

    #[Test]
    public function can_return_keys_as_set_from_hstore_literal(): void
    {
        $dql = "SELECT HSTORE_SKEYS('\"a\"=>\"1\",\"b\"=>\"2\"') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $keys = \array_column($result, 'result');
        $this->assertEqualsCanonicalizing(['a', 'b'], $keys);
    }

    #[Test]
    public function can_return_keys_as_set_from_entity_property(): void
    {
        $dql = 'SELECT HSTORE_SKEYS(t.data) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsHstores t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $keys = \array_column($result, 'result');
        $this->assertEqualsCanonicalizing(['a', 'b', 'c'], $keys);
    }
}
