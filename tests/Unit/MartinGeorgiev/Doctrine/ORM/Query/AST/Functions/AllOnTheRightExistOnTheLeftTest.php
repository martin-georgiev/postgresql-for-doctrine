<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\AllOnTheRightExistOnTheLeft;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Arr;

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
            'checks if single key exists in jsonb' => "SELECT (c0_.object1 ??& ARRAY['test']) AS sclr_0 FROM ContainsJsons c0_",
            'checks if multiple keys exist in jsonb' => "SELECT (c0_.object1 ??& ARRAY['key1', 'key2']) AS sclr_0 FROM ContainsJsons c0_",
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if single key exists in jsonb' => \sprintf("SELECT ALL_ON_RIGHT_EXIST_ON_LEFT(e.object1, ARRAY('test')) FROM %s e", ContainsJsons::class),
            'checks if multiple keys exist in jsonb' => \sprintf("SELECT ALL_ON_RIGHT_EXIST_ON_LEFT(e.object1, ARRAY('key1', 'key2')) FROM %s e", ContainsJsons::class),
        ];
    }
}
