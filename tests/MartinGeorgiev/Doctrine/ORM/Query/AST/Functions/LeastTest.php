<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Least;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsSeveralIntegers;

class LeastTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'LEAST' => Least::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT least(c0_.integer1,c0_.integer2,c0_.integer3) AS sclr_0 FROM ContainsSeveralIntegers c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT LEAST(e.integer1, e.integer2, e.integer3) FROM %s e', ContainsSeveralIntegers::class);
    }
}
