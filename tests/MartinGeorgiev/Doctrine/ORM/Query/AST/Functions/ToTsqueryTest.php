<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class ToTsqueryTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT to_tsquery(c0_.text) AS sclr_0 FROM ContainsText c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT TO_TSQUERY(e.text) FROM %s e', ContainsText::class);
    }
}
