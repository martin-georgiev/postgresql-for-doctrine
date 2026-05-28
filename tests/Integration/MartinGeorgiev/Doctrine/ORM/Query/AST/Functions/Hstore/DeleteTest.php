<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Delete;
use PHPUnit\Framework\Attributes\Test;

class DeleteTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_DELETE' => Delete::class,
        ];
    }

    #[Test]
    public function can_delete_key_from_hstore_literal(): void
    {
        $dql = "SELECT HSTORE_DELETE('\"a\"=>\"1\",\"b\"=>\"2\"', 'a') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"b"=>"2"', $result[0]['result']);
    }

    #[Test]
    public function can_delete_key_from_entity_property(): void
    {
        // Row 3 has exactly two keys so deleting one yields a deterministic single-pair result.
        $dql = "SELECT HSTORE_DELETE(t.data, 'key1') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 3";

        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"key2"=>"value2"', $result[0]['result']);
    }
}
