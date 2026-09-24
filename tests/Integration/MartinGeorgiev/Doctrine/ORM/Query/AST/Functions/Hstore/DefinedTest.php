<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Hstore\Defined;
use PHPUnit\Framework\Attributes\Test;

final class DefinedTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'HSTORE_DEFINED' => Defined::class,
        ];
    }

    #[Test]
    public function returns_whether_the_key_is_defined_from_an_hstore_literal(): void
    {
        $dql = "SELECT HSTORE_DEFINED('\"a\"=>\"1\",\"b\"=>\"2\"', 'a') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }

    #[Test]
    public function returns_whether_the_key_is_defined_from_an_entity_field(): void
    {
        $dql = "SELECT HSTORE_DEFINED(t.data, 'a') as result
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsHstores t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
