<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Degrees;
use PHPUnit\Framework\TestCase;

class DegreesTest extends TestCase
{
    public function test_get_function_name(): void
    {
        $degrees = new Degrees('dummy');
        $this->assertSame('DEGREES', $degrees->getFunctionName());
    }
}
