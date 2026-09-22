<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi;
use PHPUnit\Framework\Attributes\Test;

final class PiTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'PI' => Pi::class,
        ];
    }

    #[Test]
    public function returns_the_value_of_pi(): void
    {
        $dql = 'SELECT PI() as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.141592653589793, $result[0]['result']);
    }
}
