<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\GenRandomUuid;
use PHPUnit\Framework\Attributes\Test;

final class GenRandomUuidTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'GEN_RANDOM_UUID' => GenRandomUuid::class,
        ];
    }

    #[Test]
    public function generates_random_uuid(): void
    {
        $dql = 'SELECT GEN_RANDOM_UUID() as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $uuid = $result[0]['result'];

        $this->assertIsString($uuid);
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid);
    }

    #[Test]
    public function generates_unique_uuids(): void
    {
        $dql = 'SELECT GEN_RANDOM_UUID() as uuid1, GEN_RANDOM_UUID() as uuid2
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t
                WHERE t.id = 1';

        $result = $this->executeDqlQuery($dql);
        $uuid1 = $result[0]['uuid1'];
        $uuid2 = $result[0]['uuid2'];

        $this->assertNotEquals($uuid1, $uuid2);
    }
}
