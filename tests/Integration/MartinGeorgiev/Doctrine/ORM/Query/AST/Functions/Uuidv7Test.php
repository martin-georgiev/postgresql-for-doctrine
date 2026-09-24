<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Uuidv7;
use PHPUnit\Framework\Attributes\Test;

final class Uuidv7Test extends NumericTestCase
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
    public function returns_a_uuid(): void
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
    public function returns_a_uuid_with_a_shift(): void
    {
        $dql = "SELECT UUIDV7('1 hour') as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1";

        $result = $this->executeDqlQuery($dql);
        $uuid = $result[0]['result'];

        $this->assertIsString($uuid);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }
}
