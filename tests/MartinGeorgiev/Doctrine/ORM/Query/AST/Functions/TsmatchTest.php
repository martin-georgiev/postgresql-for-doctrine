<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsquery;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ToTsvector;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Tsmatch;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class TsmatchTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'TO_TSQUERY' => ToTsquery::class,
            'TO_TSVECTOR' => ToTsvector::class,
            'TSMATCH' => Tsmatch::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT (to_tsvector(c0_.text) @@ to_tsquery('testing')) AS sclr_0 FROM ContainsText c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT TSMATCH(TO_TSVECTOR(e.text), TO_TSQUERY('testing')) FROM %s e", ContainsText::class);
    }
}
