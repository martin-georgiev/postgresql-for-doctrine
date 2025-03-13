<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL check if the right-side text exists on the left-side JSONB (using ?).
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 2.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL with boolean comparison: "WHERE RIGHT_EXISTS_ON_LEFT(e.jsonbData, 'key') = TRUE"
 * Returns boolean, must be used with "= TRUE" or "= FALSE" in DQL.
 */
class TheRightExistsOnTheLeft extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s ?? %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
