<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Numrange;
use PHPUnit\Framework\Attributes\Test;

class NumrangeTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'NUMRANGE' => Numrange::class,
        ];
    }

    #[Test]
    public function numrange(): void
    {
        $dql = 'SELECT NUMRANGE(t.decimal1, t.decimal2) as result FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t WHERE t.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('[10.5,20.5)', $result[0]['result']);
    }

    #[Test]
    public function numrange_with_bounds(): void
    {
        $dql = "SELECT NUMRANGE(t.decimal1, t.decimal2, '(]') as result FROM Fixtures\\MartinGeorgiev\\Doctrine\\Entity\\ContainsNumerics t WHERE t.id = 1";
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('(10.5,20.5]', $result[0]['result']);
    }
}
