<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ?| operator.
 *
 * Checks if any key on the right exists in the left JSONB object.
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 2.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE ANY_ON_RIGHT_EXISTS_ON_LEFT(e.jsonbObject, ARR('key1', 'key2')) = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class AnyOnTheRightExistsOnTheLeft extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s ??| %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
