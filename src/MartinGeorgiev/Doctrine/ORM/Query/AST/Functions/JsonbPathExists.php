<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL JSONB_PATH_EXISTS().
 *
 * Checks whether the JSON path returns any item for the specified JSON value.
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT JSONB_PATH_EXISTS(e.jsonbData, '$.a[*] ? (@ > 2)')"
 */
class JsonbPathExists extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_path_exists(%s)');
    }

    protected function validateArguments(Node ...$arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 2 || $argumentCount > 4) {
            throw InvalidArgumentForVariadicFunctionException::between('jsonb_path_exists', 2, 4);
        }

        // Validate that the fourth parameter is a valid boolean if provided
        if ($argumentCount === 4) {
            $this->validateBoolean($arguments[3], 'JSONB_PATH_EXISTS');
        }
    }
}
