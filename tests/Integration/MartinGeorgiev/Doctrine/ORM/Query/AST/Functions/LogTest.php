<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log;
use PHPUnit\Framework\Attributes\Test;

final class LogTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'LOG' => Log::class,
        ];
    }

    #[Test]
    public function returns_the_logarithm_from_numeric_literals(): void
    {
        $dql = 'SELECT LOG(10, 100) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_logarithm_from_an_entity_field(): void
    {
        $dql = 'SELECT LOG(10, n.decimal1) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1.021189299069938, $result[0]['result']);
    }
}
