<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayLength;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayLengthTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ARRAY_LENGTH' => ArrayLength::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT array_length(c0_.array, 1) AS sclr_0 FROM ContainsArray c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT ARRAY_LENGTH(e.array, 1) FROM %s e', ContainsArray::class);
    }
}
