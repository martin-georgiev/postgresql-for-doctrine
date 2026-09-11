<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST;

use Doctrine\ORM\Query\AST\Node;

/**
 * Represents a literal SQL NULL.
 *
 * Doctrine's Literal node carries STRING, BOOLEAN and NUMERIC but has no NULL type, and NewValue() returns
 * PHP null rather than a Node for a literal NULL token. BaseVariadicFunction wraps that result in this node
 * to keep the argument a Node while still rendering as SQL NULL.
 *
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
final class NullLiteral extends Node
{
    /**
     * The walker is typed `mixed` because Doctrine ORM 2.14 declares the parameter untyped,
     * and narrowing it to SqlWalker there would break parameter contravariance.
     */
    public function dispatch(mixed $sqlWalker): string
    {
        return 'NULL';
    }
}
