<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseVariadicFunction;

abstract class BaseVariadicFunctionTestCase extends TestCase
{
    abstract protected function createFixture(): BaseVariadicFunction;
}
