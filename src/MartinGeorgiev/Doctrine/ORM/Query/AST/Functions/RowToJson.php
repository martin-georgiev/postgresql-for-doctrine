<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL ROW_TO_JSON().
 *
 * Returns the row as a JSON object. Line feeds will be added between level-1 elements if pretty_bool is true.
 *
 * @see https://www.postgresql.org/docs/16/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ROW_TO_JSON(e.row) FROM Entity e"
 * @example Using it in DQL with pretty_bool: "SELECT ROW_TO_JSON(e.row, 'true') FROM Entity e"
 */
class RowToJson extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('row_to_json(%s)');
    }

    protected function validateArguments(Node ...$arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 1 || $argumentCount > 2) {
            throw InvalidArgumentForVariadicFunctionException::between('row_to_json', 1, 2);
        }

        // Validate that the second parameter is a valid boolean if provided
        if ($argumentCount === 2) {
            $this->validateBoolean($arguments[1], 'ROW_TO_JSON');
        }
    }
}
