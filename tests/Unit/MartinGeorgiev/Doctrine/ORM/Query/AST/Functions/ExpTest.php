<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exp;
use PHPUnit\Framework\TestCase;

class ExpTest extends TestCase
{
    public function test_get_function_name(): void
    {
        $exp = new Exp('dummy');
        $this->assertSame('EXP', $exp->getFunctionName());
    }
}
