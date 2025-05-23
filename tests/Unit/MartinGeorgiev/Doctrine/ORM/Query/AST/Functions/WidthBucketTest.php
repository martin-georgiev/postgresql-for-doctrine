<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDecimals;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket;

class WidthBucketTest extends TestCase
{
    protected function getStringFunctions(): array
    {
        return [
            'WIDTH_BUCKET' => WidthBucket::class,
        ];
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'SELECT WIDTH_BUCKET(c0_.decimal1, 0.0, 20.0, 4) AS sclr_0 FROM ContainsDecimals c0_',
            'SELECT WIDTH_BUCKET(15, 0.0, 20.0, 4) AS sclr_0 FROM ContainsDecimals c0_',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            \sprintf('SELECT WIDTH_BUCKET(e.decimal1, 0.0, 20.0, 4) FROM %s e', ContainsDecimals::class),
            \sprintf('SELECT WIDTH_BUCKET(15, 0.0, 20.0, 4) FROM %s e', ContainsDecimals::class),
        ];
    }
}
