<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class ToTsvectorTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'TO_TSVECTOR' => ToTsvector::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT to_tsvector(c0_.text) AS sclr_0 FROM ContainsText c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT TO_TSVECTOR(e.text) FROM %s e', ContainsText::class);
    }
}
