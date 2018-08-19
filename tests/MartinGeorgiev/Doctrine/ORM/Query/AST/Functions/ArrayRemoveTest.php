<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayRemove;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayRemoveTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ARRAY_REMOVE' => ArrayRemove::class,
        ];
    }

    /**
     * @return array
     */
    protected function getExpectedSql()
    {
        return [
            'SELECT array_remove(c0_.array, 1944) AS sclr_0 FROM ContainsArray c0_',
            "SELECT array_remove(c0_.array, 'peach') AS sclr_0 FROM ContainsArray c0_",
        ];
    }

    /**
     * @return array
     */
    protected function getDql()
    {
        return [
            sprintf('SELECT ARRAY_REMOVE(e.array, 1944) FROM %s e', ContainsArray::class),
            sprintf("SELECT ARRAY_REMOVE(e.array, 'peach') FROM %s e", ContainsArray::class),
        ];
    }
}
