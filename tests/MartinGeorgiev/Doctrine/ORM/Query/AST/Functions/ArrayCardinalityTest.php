<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCardinality;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayCardinalityTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ARRAY_CARDINALITY' => ArrayCardinality::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT cardinality(c0_.array) AS sclr_0 FROM ContainsArray c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT ARRAY_CARDINALITY(e.array) FROM %s e', ContainsArray::class);
    }
}
