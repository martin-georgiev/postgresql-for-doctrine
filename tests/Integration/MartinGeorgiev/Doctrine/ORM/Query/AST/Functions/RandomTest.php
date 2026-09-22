<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random;
use PHPUnit\Framework\Attributes\Test;

final class RandomTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'RANDOM' => Random::class,
        ];
    }

    #[Test]
    public function returns_a_random_value(): void
    {
        $dql = 'SELECT RANDOM() as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertGreaterThanOrEqual(0.0, $result[0]['result']);
        $this->assertLessThanOrEqual(1.0, $result[0]['result']);
    }
}
