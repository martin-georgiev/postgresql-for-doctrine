<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL JSONB_SET().
 *
 * Returns the target jsonb with the section designated by path replaced by the new value,
 * or with the new value added if create_missing is true (default is true) and the item
 * designated by path does not exist.
 *
 * As with the path orientated operators, negative integers that appear in path count from the end
 * of JSON arrays.
 *
 * @see https://www.postgresql.org/docs/16/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with path and value: "SELECT JSONB_SET(e.jsonbData, '{address,city}', '\"Sofia\"') FROM Entity e"
 * @example Using it in DQL with create_if_missing flag: "SELECT JSONB_SET(e.jsonbData, '{address,city}', '\"Sofia\"', false) FROM Entity e"
 */
class JsonbSet extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_set(%s)');
    }

    protected function validateArguments(Node ...$arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 3 || $argumentCount > 4) {
            throw InvalidArgumentForVariadicFunctionException::between('jsonb_set', 3, 4);
        }

        // Validate that the fourth parameter is a valid boolean if provided
        if ($argumentCount === 4) {
            $this->validateBoolean($arguments[3], 'JSONB_SET');
        }
    }
}
