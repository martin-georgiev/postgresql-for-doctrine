<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Cbrt;
use PHPUnit\Framework\Attributes\Test;

final class CbrtTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'CBRT' => Cbrt::class,
        ];
    }

    #[Test]
    public function returns_the_cube_root_from_a_numeric_literal(): void
    {
        $dql = 'SELECT CBRT(27.0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(3.0, $result[0]['result']);
    }

    #[Test]
    public function returns_the_cube_root_from_an_entity_field(): void
    {
        $dql = 'SELECT CBRT(n.decimal1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(2.1897595699439445, $result[0]['result']);
    }
}
