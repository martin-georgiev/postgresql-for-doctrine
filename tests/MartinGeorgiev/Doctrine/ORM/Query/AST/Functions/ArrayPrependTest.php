<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayPrepend;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayPrependTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ARRAY_PREPEND' => ArrayPrepend::class,
        ];
    }

    /**
     * @return array
     */
    protected function getExpectedSql()
    {
        return [
            'SELECT array_prepend(1885, c0_.array) AS sclr_0 FROM ContainsArray c0_',
            "SELECT array_prepend('red', c0_.array) AS sclr_0 FROM ContainsArray c0_",
        ];
    }

    /**
     * @return array
     */
    protected function getDql()
    {
        return [
            sprintf('SELECT ARRAY_PREPEND(1885, e.array) FROM %s e', ContainsArray::class),
            sprintf("SELECT ARRAY_PREPEND('red', e.array) FROM %s e", ContainsArray::class),
        ];
    }
}
