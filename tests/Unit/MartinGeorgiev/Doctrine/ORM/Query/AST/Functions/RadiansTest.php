<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Radians;
use PHPUnit\Framework\TestCase;

class RadiansTest extends TestCase
{
    public function test_get_function_name(): void
    {
        $radians = new Radians('dummy');
        $this->assertSame('RADIANS', $radians->getFunctionName());
    }
}
