<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Fuzzystrmatch;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;

/**
 * Implementation of PostgreSQL DAITCH_MOKOTOFF().
 *
 * Generates the Daitch-Mokotoff soundex codes for its input.
 * The result may contain one or more codes depending on how many plausible pronunciations there are,
 * so it is represented as an array.
 *
 * @see https://www.postgresql.org/docs/17/fuzzystrmatch.html
 * @since 4.2
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DAITCH_MOKOTOFF(e.name) FROM Entity e"
 */
class DaitchMokotoff extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('daitch_mokotoff(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
