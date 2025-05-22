<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Pi;
use PHPUnit\Framework\TestCase;

class PiTest extends TestCase
{
    public function test_get_function_name(): void
    {
        $pi = new Pi('dummy');
        $this->assertSame('PI', $pi->getFunctionName());
    }
}
