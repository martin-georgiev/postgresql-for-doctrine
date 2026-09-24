<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonScalar;
use PHPUnit\Framework\Attributes\Test;

final class JsonScalarTest extends JsonTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'JSON_SCALAR function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'JSON_SCALAR' => JsonScalar::class,
        ];
    }

    #[Test]
    public function converts_the_value_to_a_json_scalar_from_a_text_literal(): void
    {
        $dql = "SELECT JSON_SCALAR('hello') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"hello"', $result[0]['result']);
    }

    #[Test]
    public function converts_the_value_to_a_json_scalar_from_an_entity_field(): void
    {
        $dql = 'SELECT JSON_SCALAR(t.id) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons t 
                WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1', $result[0]['result']);
    }
}
