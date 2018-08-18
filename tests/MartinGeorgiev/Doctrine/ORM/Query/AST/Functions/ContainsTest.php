<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Contains;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ContainsTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'CONTAINS' => Contains::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT (c0_.array @> '{681,1185,1878}') AS sclr_0 FROM ContainsArray c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT CONTAINS(e.array, '{681,1185,1878}') FROM %s e", ContainsArray::class);
    }
}
