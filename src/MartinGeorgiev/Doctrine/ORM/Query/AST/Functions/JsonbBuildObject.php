<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;

/**
 * Implementation of PostgreSQL JSONB_BUILD_OBJECT().
 *
 * @see https://www.postgresql.org/docs/17/functions-json.html
 * @since 2.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonbBuildObject extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_build_object(%s)');
    }

    protected function validateArguments(Node ...$arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount === 0 || $argumentCount % 2 !== 0) {
            throw InvalidArgumentForVariadicFunctionException::evenNumber('jsonb_build_object');
        }
    }
}
