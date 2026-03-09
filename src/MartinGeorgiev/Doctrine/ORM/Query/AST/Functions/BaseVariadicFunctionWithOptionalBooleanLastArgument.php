<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseVariadicFunctionWithOptionalBooleanLastArgument extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        if (\count($arguments) === $this->getMaxArgumentCount()) {
            $this->validateBoolean($arguments[\count($arguments) - 1], $this->getFunctionName());
        }
    }
}
