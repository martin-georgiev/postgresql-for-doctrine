<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL ?& operator.
 *
 * Checks if all keys on the right exist in the left JSONB object.
 *
 * @see https://www.postgresql.org/docs/14/functions-json.html
 * @since 2.3
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT ALL_ON_RIGHT_EXIST_ON_LEFT(e.jsonbObject, ARR('key1', 'key2')) FROM Entity e"
 */
class AllOnTheRightExistOnTheLeft extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('(%s ??& %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
