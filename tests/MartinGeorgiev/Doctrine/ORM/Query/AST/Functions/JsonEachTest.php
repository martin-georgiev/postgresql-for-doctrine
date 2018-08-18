<?php

namespace MartinGeorgiev\Tests\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\JsonEach;
use MartinGeorgiev\Tests\Doctrine\Fixtures\Entity\ContainsJson;

class JsonEachTest extends TestCase
{
    /**
     * @return array
     */
    protected function getStringFunctions()
    {
        return [
            'JSON_EACH' => JsonEach::class,
        ];
    }

    /**
     * @return string
     */
    protected function getExpectedSql()
    {
        return 'SELECT json_each(c0_.object) AS sclr_0 FROM ContainsJson c0_';
    }

    /**
     * @return string
     */
    protected function getDql()
    {
        return sprintf('SELECT JSON_EACH(e.object) FROM %s e', ContainsJson::class);
    }
}
