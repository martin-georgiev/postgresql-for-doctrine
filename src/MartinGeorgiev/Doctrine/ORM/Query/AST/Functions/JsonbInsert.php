<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidArgumentForVariadicFunctionException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\BooleanValidationTrait;

/**
 * Implementation of PostgreSQL JSONB_INSERT().
 *
 * Inserts a new value into a JSONB field at the specified path.
 * If the path already exists, the value is not changed unless the last parameter is true.
 *
 * @see https://www.postgresql.org/docs/16/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with path and value: "SELECT JSONB_INSERT(e.jsonbData, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}') FROM Entity e"
 * @example Using it in DQL with create_if_missing flag: "SELECT JSONB_INSERT(e.jsonbData, '{country}', '{\"iso_3166_a3_code\":\"BGR\"}', true) FROM Entity e"
 */
class JsonbInsert extends BaseVariadicFunction
{
    use BooleanValidationTrait;

    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('jsonb_insert(%s)');
    }

    protected function validateArguments(Node ...$arguments): void
    {
        $argumentCount = \count($arguments);
        if ($argumentCount < 3 || $argumentCount > 4) {
            throw InvalidArgumentForVariadicFunctionException::between('jsonb_insert', 3, 4);
        }

        // Validate that the fourth parameter is a valid boolean if provided
        if ($argumentCount === 4) {
            $this->validateBoolean($arguments[3], 'JSONB_INSERT');
        }
    }
}
