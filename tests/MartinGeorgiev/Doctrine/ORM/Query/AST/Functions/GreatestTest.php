<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Greatest;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsSeveralIntegers;

class GreatestTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'GREATEST' => Greatest::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT greatest(c0_.integer1,c0_.integer2,c0_.integer3) AS sclr_0 FROM ContainsSeveralIntegers c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT GREATEST(e.integer1, e.integer2, e.integer3) FROM %s e', ContainsSeveralIntegers::class);
    }
}
