<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayReplace;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayReplaceTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ARRAY_REPLACE' => ArrayReplace::class,
        ];
    }

    /**
     * @return array
     */
    protected function getExpectedSql()
    {
        return [
            'SELECT array_replace(c0_.array, 1939, 1957) AS sclr_0 FROM ContainsArray c0_',
            "SELECT array_replace(c0_.array, 'green', 'mint') AS sclr_0 FROM ContainsArray c0_",
        ];
    }

    /**
     * @return array
     */
    protected function getDql()
    {
        return [
            sprintf('SELECT ARRAY_REPLACE(e.array, 1939, 1957) FROM %s e', ContainsArray::class),
            sprintf("SELECT ARRAY_REPLACE(e.array, 'green', 'mint') FROM %s e", ContainsArray::class),
        ];
    }
}
