<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\StringToArray;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsText;

class StringToArrayTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'STRING_TO_ARRAY' => StringToArray::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return "SELECT string_to_array(c0_.text, ',') AS sclr_0 FROM ContainsText c0_";
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf("SELECT STRING_TO_ARRAY(e.text, ',') FROM %s e", ContainsText::class);
    }
}
