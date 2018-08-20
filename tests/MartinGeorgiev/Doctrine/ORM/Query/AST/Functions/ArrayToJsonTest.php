<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\ArrayToJson;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsArray;

class ArrayToJsonTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'ARRAY_TO_JSON' => ArrayToJson::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT array_to_json(c0_.array) AS sclr_0 FROM ContainsArray c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT ARRAY_TO_JSON(e.array) FROM %s e', ContainsArray::class);
    }
}
