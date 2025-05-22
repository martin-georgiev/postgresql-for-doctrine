<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function test_get_function_name(): void
    {
        $log = new Log('dummy');
        $this->assertSame('LOG', $log->getFunctionName());
    }
}
