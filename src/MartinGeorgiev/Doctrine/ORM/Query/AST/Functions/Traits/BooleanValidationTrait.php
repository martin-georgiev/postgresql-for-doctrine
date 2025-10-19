<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidBooleanException;

/**
 * Provides boolean validation functionality for functions that accept boolean parameters.
 *
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait BooleanValidationTrait
{
    /**
     * Validates that the given node represents a valid boolean value.
     *
     * @throws InvalidBooleanException If the value is not a valid boolean
     */
    protected function validateBoolean(Node $node, string $functionName): void
    {
        if (!$node instanceof Literal || !\is_string($node->value)) {
            throw InvalidBooleanException::forNonLiteralNode($node::class, $functionName);
        }

        $value = \strtolower(\trim((string) $node->value, "'\""));
        $lowercaseValue = \strtolower($value);

        if (!$this->isValidBoolean($lowercaseValue)) {
            throw InvalidBooleanException::forInvalidBoolean($value, $functionName);
        }
    }

    private function isValidBoolean(string $boolean): bool
    {
        return \in_array($boolean, ['true', 'false'], true);
    }
}
