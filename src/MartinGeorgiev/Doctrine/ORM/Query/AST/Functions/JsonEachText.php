<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Implementation of PostgreSQL JSON_EACH_TEXT().
 *
 * @see https://www.postgresql.org/docs/9.4/static/functions-json.html
 * @since 0.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class JsonEachText extends BaseFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('json_each_text(%s)');
        $this->addNodeMapping('StringPrimary');
    }
}
