<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Ln;
use PHPUnit\Framework\TestCase;

class LnTest extends TestCase
{
    public function test_get_function_name(): void
    {
        $ln = new Ln('dummy');
        $this->assertSame('LN', $ln->getFunctionName());
    }
}
