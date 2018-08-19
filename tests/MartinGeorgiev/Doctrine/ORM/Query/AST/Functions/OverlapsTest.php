<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Overlaps;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class OverlapsTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'OVERLAPS' => Overlaps::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT (c0_.array && '{681,1185,1878}') AS sclr_0 FROM ContainsArray c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT OVERLAPS(e.array, '{681,1185,1878}') FROM %s e", ContainsArray::class);
    }
}
