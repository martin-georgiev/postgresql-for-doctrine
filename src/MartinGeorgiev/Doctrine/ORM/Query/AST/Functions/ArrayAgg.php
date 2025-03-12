<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * Implementation of PostgreSQL ARRAY_AGG().
 *
 * @see https://www.postgresql.org/docs/17/functions-aggregate.html
 * @since 1.4
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class ArrayAgg extends BaseOrderableFunction
{
    protected function customizeFunction(): void
    {
        $this->setFunctionPrototype('array_agg(%s%s)');
    }

    protected function parseFunction(Parser $parser): void
    {
        $this->expression = $parser->StringPrimary();
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $dispatched = [
            $this->expression->dispatch($sqlWalker),
            $this->getOptionalOrderByClause($sqlWalker),
        ];

        return \vsprintf($this->functionPrototype, $dispatched);
    }
}
