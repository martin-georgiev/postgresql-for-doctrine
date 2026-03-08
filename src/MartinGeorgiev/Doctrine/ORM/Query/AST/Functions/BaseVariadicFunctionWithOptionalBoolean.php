<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Base class for variadic DQL functions whose last argument is an optional boolean.
 *
 * Subclasses only need to implement getFunctionName(), getNodeMappingPattern(),
 * getMinArgumentCount(), and getMaxArgumentCount(). The boolean validation of the
 * final argument when the maximum argument count is reached is handled here.
 *
 * @since 4.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseVariadicFunctionWithOptionalBoolean extends BaseVariadicFunction
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
