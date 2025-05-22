<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign;

class SignTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIGN' => Sign::class,
        ];
    }

    public function test_sign_with_zero(): void
    {
        $dql = 'SELECT SIGN(0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame(0.0, $result[0]['result']);
    }

    public function test_sign_with_column_value(): void
    {
        $dql = 'SELECT SIGN(n.decimal1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertSame('1', $result[0]['result']);
    }
}
