<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Sign;
use PHPUnit\Framework\Attributes\Test;

class SignTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'SIGN' => Sign::class,
        ];
    }

    #[Test]
    public function sign_with_zero(): void
    {
        $dql = 'SELECT SIGN(0) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(0, $result[0]['result']);
    }

    #[Test]
    public function sign_with_column_value(): void
    {
        $dql = 'SELECT SIGN(n.decimal1) as result 
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics n 
                WHERE n.id = 1';
        $result = $this->executeDqlQuery($dql);
        $this->assertEquals(1, $result[0]['result']);
    }
}
