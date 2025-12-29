<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonScalar;
use PHPUnit\Framework\Attributes\Test;

class JsonScalarTest extends JsonTestCase
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
    public function can_convert_string_to_json_scalar(): void
    {
        $dql = "SELECT JSON_SCALAR('hello') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"hello"', $result[0]['result']);
    }

    #[Test]
    public function can_convert_number_to_json_scalar(): void
    {
        $dql = "SELECT JSON_SCALAR('42') as result 
                FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsJsons t 
                WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('"42"', $result[0]['result']);
    }
}
