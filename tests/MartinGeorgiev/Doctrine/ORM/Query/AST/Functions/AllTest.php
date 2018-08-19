<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\All;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class AllTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ALL_OF' => All::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT c0_.id AS id_0 FROM ContainsArray c0_ WHERE c0_.id > ALL(c0_.array)';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT e.id FROM %s e WHERE e.id > ALL_OF(e.array)', ContainsArray::class);
    }
}
