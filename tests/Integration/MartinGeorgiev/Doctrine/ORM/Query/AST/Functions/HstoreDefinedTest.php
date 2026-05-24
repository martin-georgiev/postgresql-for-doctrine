<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\HstoreDefined;
use PHPUnit\Framework\Attributes\Test;

class HstoreDefinedTest extends HstoreTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_DEFINED' => HstoreDefined::class,
        ];
    }

    #[Test]
    public function can_check_if_key_is_defined_in_hstore_literal(): void
    {
        $dql = "SELECT HSTORE_DEFINED('\"a\"=>\"1\",\"b\"=>\"2\"', 'a') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue((bool) $result[0]['result']);
    }

    #[Test]
    public function can_check_if_key_is_defined_in_entity_property(): void
    {
        $dql = "SELECT HSTORE_DEFINED(t.data, 'a') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue((bool) $result[0]['result']);
    }

    #[Test]
    public function returns_false_for_key_with_null_value(): void
    {
        $dql = "SELECT HSTORE_DEFINED(t.data, 'y') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 2";

        $result = $this->executeDqlQuery($dql);
        $this->assertFalse((bool) $result[0]['result']);
    }
}
