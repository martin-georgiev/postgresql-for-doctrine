<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonExists;
use PHPUnit\Framework\Attributes\Test;

final class JsonExistsTest extends JsonTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'JSON_EXISTS function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSON_EXISTS' => JsonExists::class,
        ];
    }

    #[Test]
    public function returns_true_when_the_path_exists_in_an_entity_field(): void
    {
        $dql = "SELECT JSON_EXISTS(t.jsonObject1, '$.name') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertTrue($result[0]['result']);
    }
}
