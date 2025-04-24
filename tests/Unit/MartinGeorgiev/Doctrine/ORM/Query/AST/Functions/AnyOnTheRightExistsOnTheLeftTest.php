<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

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
            'checks if any key exists in jsonb' => "SELECT (c0_.object1 ??| ARRAY['test']) AS sclr_0 FROM ContainsJsons c0_",
            'checks if any of multiple keys exist in jsonb' => "SELECT (c0_.object1 ??| ARRAY['key1', 'key2']) AS sclr_0 FROM ContainsJsons c0_",
            'checks with parameter' => 'SELECT (c0_.object1 ??| ?) AS sclr_0 FROM ContainsJsons c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'checks if any key exists in jsonb' => \sprintf("SELECT ANY_ON_RIGHT_EXISTS_ON_LEFT(e.object1, ARRAY('test')) FROM %s e", ContainsJsons::class),
            'checks if any of multiple keys exist in jsonb' => \sprintf("SELECT ANY_ON_RIGHT_EXISTS_ON_LEFT(e.object1, ARRAY('key1', 'key2')) FROM %s e", ContainsJsons::class),
            'checks with parameter' => \sprintf('SELECT ANY_ON_RIGHT_EXISTS_ON_LEFT(e.object1, :parameter) FROM %s e', ContainsJsons::class),
        ];
    }
}
