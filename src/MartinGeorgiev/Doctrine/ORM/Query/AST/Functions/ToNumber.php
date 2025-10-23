<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL to_number().
 *
 * Supports Roman numeral conversion via RN pattern (PostgreSQL 18+).
 *
 * @see https://www.postgresql.org/docs/18/functions-formatting.html
 * @since 3.3.0
 */
class ToNumber extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('to_number(%s, %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }
}
