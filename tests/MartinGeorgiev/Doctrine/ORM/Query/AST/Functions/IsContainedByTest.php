<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\IsContainedBy;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class IsContainedByTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'IS_CONTAINED_BY' => IsContainedBy::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT (c0_.array <@ '{681,1185,1878}') AS sclr_0 FROM ContainsArray c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT IS_CONTAINED_BY(e.array, '{681,1185,1878}') FROM %s e", ContainsArray::class);
    }
}
