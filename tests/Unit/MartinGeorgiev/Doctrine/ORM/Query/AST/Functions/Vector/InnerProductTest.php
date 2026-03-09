<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsVectors;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Vector\InnerProduct;
use Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class InnerProductTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'INNER_PRODUCT' => InnerProduct::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'calculates inner product between two column vectors' => 'SELECT inner_product(c0_.vector1, c0_.vector2) AS sclr_0 FROM ContainsVectors c0_',
            'calculates inner product between column and literal vector' => "SELECT inner_product(c0_.vector1, '[1,2,3]') AS sclr_0 FROM ContainsVectors c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'calculates inner product between two column vectors' => \sprintf('SELECT INNER_PRODUCT(e.vector1, e.vector2) FROM %s e', ContainsVectors::class),
            'calculates inner product between column and literal vector' => \sprintf("SELECT INNER_PRODUCT(e.vector1, '[1,2,3]') FROM %s e", ContainsVectors::class),
        ];
    }
}
