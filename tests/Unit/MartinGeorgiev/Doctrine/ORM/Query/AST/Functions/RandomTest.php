<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Random;
use PHPUnit\Framework\TestCase;

class RandomTest extends TestCase
{
    public function test_get_function_name(): void
    {
        $random = new Random('dummy');
        $this->assertSame('RANDOM', $random->getFunctionName());
    }
}
