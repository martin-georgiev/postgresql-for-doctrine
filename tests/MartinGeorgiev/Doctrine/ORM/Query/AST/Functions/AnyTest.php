<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Any;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class AnyTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ANY_OF' => Any::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT c0_.id AS id_0 FROM ContainsArray c0_ WHERE c0_.id > ANY(c0_.array)';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT e.id FROM %s e WHERE e.id > ANY_OF(e.array)', ContainsArray::class);
    }
}
