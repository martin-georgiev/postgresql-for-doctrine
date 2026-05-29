<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\RandomNormal;
use PHPUnit\Framework\Attributes\Test;

final class RandomNormalTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANDOM_NORMAL' => RandomNormal::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->requirePostgresVersion(170000, 'RANDOM_NORMAL');
    }

    #[Test]
    public function generates_random_normal_without_arguments(): void
    {
        $dql = 'SELECT RANDOM_NORMAL() as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }

    #[Test]
    public function generates_random_normal_of_literals(): void
    {
        $dql = 'SELECT RANDOM_NORMAL(100, 10) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }

    #[Test]
    public function generates_random_normal_with_entity_properties(): void
    {
        $dql = 'SELECT RANDOM_NORMAL(n.decimal1, n.decimal2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertNotNull($result[0]['result']);
    }
}
