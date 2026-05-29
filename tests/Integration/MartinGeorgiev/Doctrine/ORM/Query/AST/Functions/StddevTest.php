<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Stddev;
use PHPUnit\Framework\Attributes\Test;

class StddevTest extends NumericTestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'STDDEV' => Stddev::class,
        ];
    }

    #[Test]
    public function returns_null_for_single_row_set_on_integer_column(): void
    {
        $dql = 'SELECT STDDEV(t.integer1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }

    #[Test]
    public function returns_null_for_single_row_set_on_decimal_column(): void
    {
        $dql = 'SELECT STDDEV(t.decimal1) as result
                FROM Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics t';
        $result = $this->executeDqlQuery($dql);
        $this->assertNull($result[0]['result']);
    }
}
