<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL PG_INPUT_IS_VALID().
 *
 * Tests whether a string is valid input for a type, without failing the query when it is not.
 *
 * @see https://www.postgresql.org/docs/18/functions-info.html#FUNCTIONS-INFO-VALIDITY
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE PG_INPUT_IS_VALID(e.raw, 'integer') = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" when used in WHERE clause in DQL.
 */
class PgInputIsValid extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('pg_input_is_valid(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
