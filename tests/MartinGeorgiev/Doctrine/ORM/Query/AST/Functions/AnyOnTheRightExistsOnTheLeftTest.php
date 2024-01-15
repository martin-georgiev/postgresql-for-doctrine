<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AnyOnTheRightExistsOnTheLeft;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;

class AnyOnTheRightExistsOnTheLeftTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ANY_ON_RIGHT_EXISTS_ON_LEFT' => AnyOnTheRightExistsOnTheLeft::class,
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.object1 ??| ARRAY['test']) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT ANY_ON_RIGHT_EXISTS_ON_LEFT(e.object1, ARRAY('test')) FROM %s e", ContainsJsons::class),
        ];
    }
}
