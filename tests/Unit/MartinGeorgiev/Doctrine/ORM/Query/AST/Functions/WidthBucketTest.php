<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\WidthBucket;
use PHPUnit\Framework\TestCase;

class WidthBucketTest extends TestCase
{
    public function test_get_function_name(): void
    {
        $widthBucket = new WidthBucket('dummy');
        $this->assertSame('WIDTH_BUCKET', $widthBucket->getFunctionName());
    }
}
