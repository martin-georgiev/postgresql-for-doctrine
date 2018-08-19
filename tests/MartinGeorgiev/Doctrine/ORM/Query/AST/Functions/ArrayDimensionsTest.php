<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayDimensions;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayDimensionsTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ARRAY_DIMENSIONS' => ArrayDimensions::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT array_dims(c0_.array) AS sclr_0 FROM ContainsArray c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT ARRAY_DIMENSIONS(e.array) FROM %s e', ContainsArray::class);
    }
}
