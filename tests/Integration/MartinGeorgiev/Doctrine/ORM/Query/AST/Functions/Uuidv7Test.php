<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Uuidv7;
use PHPUnit\Framework\Attributes\Test;

class Uuidv7Test extends NumericTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(180000, 'uuidv7 function');
    }

    protected function getStringFunctions(): array
    {
        return [
            'UUIDV7' => Uuidv7::class,
        ];
    }

    #[Test]
    public function can_generate_uuid_v7(): void
    {
        $dql = 'SELECT UUIDV7() as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $uuid = $result[0]['result'];

        $this->assertIsString($uuid);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }

    #[Test]
    public function can_generates_unique_uuids(): void
    {
        $dql = 'SELECT UUIDV7() as uuid1, UUIDV7() as uuid2 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t 
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $uuid1 = $result[0]['uuid1'];
        $uuid2 = $result[0]['uuid2'];

        $this->assertNotEquals($uuid1, $uuid2);
    }
}
