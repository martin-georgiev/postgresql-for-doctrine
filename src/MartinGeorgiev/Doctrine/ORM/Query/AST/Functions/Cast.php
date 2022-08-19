<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\SqlWalker;

/**
 * Implementation of PostgreSql CAST().
 *
 * @see https://www.postgresql.org/docs/current/sql-createcast.html
 * @since 1.8.0
 */
class Cast extends BaseFunction
{
    protected function customiseFunction(): void
    {
        $this->setFunctionPrototype('cast(%s AS %s)');
        $this->addNodeMapping('StringPrimary');
        $this->addNodeMapping('StringPrimary');
    }

    public function getSql(SqlWalker $sqlWalker): string
    {
        $sql = parent::getSql($sqlWalker);

        // Remove the quotes around the target type
        return preg_replace('#AS \'(\w+)\'#', 'AS $1', $sql);
    }
}
