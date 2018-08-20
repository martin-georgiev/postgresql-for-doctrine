<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayCat;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayCatTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ARRAY_CAT' => ArrayCat::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT array_cat(c0_.array, c0_.anotherArray) AS sclr_0 FROM ContainsArray c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT ARRAY_CAT(e.array, e.anotherArray) FROM %s e', ContainsArray::class);
    }
}
