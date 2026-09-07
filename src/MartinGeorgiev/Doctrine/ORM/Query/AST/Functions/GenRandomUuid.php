<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL GEN_RANDOM_UUID().
 *
 * Generates a random UUID v4.
 *
 * @see https://www.postgresql.org/docs/18/functions-uuid.html
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT GEN_RANDOM_UUID() FROM Entity e"
 */
class GenRandomUuid extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('gen_random_uuid()');
    }
}
