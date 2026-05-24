<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\HstoreDelete;
use PHPUnit\Framework\Attributes\Test;

class HstoreDeleteTest extends HstoreTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_DELETE' => HstoreDelete::class,
        ];
    }

    #[Test]
    public function can_delete_key_from_hstore_literal(): void
    {
        $dql = "SELECT HSTORE_DELETE('\"a\"=>\"1\",\"b\"=>\"2\"', 'a') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringNotContainsString('"a"=>', $result[0]['result']);
        $this->assertStringContainsString('"b"=>', $result[0]['result']);
    }

    #[Test]
    public function can_delete_key_from_entity_property(): void
    {
        $dql = "SELECT HSTORE_DELETE(t.data, 'a') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertIsString($result[0]['result']);
        $this->assertStringNotContainsString('"a"=>', $result[0]['result']);
        $this->assertStringContainsString('"b"=>', $result[0]['result']);
    }
}
