<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\HstoreAkeys;
use PHPUnit\Framework\Attributes\Test;

class HstoreAkeysTest extends HstoreTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_AKEYS' => HstoreAkeys::class,
        ];
    }

    #[Test]
    public function can_return_keys_as_array_from_hstore_literal(): void
    {
        $dql = "SELECT HSTORE_AKEYS('\"a\"=>\"1\",\"b\"=>\"2\"') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $keys = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($keys);
        $this->assertCount(2, $keys);
        $this->assertContains('a', $keys);
        $this->assertContains('b', $keys);
    }

    #[Test]
    public function can_return_keys_as_array_from_entity_property(): void
    {
        $dql = 'SELECT HSTORE_AKEYS(t.data) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsHstores t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $keys = $this->transformPostgresArray($result[0]['result']);
        $this->assertIsArray($keys);
        $this->assertCount(3, $keys);
        $this->assertContains('a', $keys);
        $this->assertContains('b', $keys);
        $this->assertContains('c', $keys);
    }
}
