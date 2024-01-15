<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\TestCase;

class AllOnTheRightExistOnTheLeftTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'ALL_ON_RIGHT_EXIST_ON_LEFT' => AllOnTheRightExistOnTheLeft::class,
            'ARRAY' => Arr::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            "SELECT (c0_.object1 ??& ARRAY['test']) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf("SELECT ALL_ON_RIGHT_EXIST_ON_LEFT(e.object1, ARRAY('test')) FROM %s e", ContainsJsons::class),
        ];
    }
}
