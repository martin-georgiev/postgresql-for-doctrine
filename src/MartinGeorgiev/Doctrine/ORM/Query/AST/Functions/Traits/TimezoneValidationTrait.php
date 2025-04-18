<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits;

use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidTimezoneException;

/**
 * Provides timezone validation functionality for functions that use valid PHP timezones.
 *
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
trait TimezoneValidationTrait
{
    /**
     * Validates that the given node represents a valid PHP timezone.
     *
     * @throws InvalidTimezoneException If the timezone is invalid
     */
    protected function validateTimezone(Node $node, string $functionName): void
    {
        if (!$node instanceof Literal || !\is_string($node->value)) {
            throw InvalidTimezoneException::forNonLiteralNode($node::class, $functionName);
        }

        $timezone = \trim((string) $node->value, "'\"");

        if (!$this->isValidTimezone($timezone)) {
            throw InvalidTimezoneException::forInvalidTimezone($timezone, $functionName);
        }
    }

    private function isValidTimezone(string $timezone): bool
    {
        try {
            new \DateTimeZone($timezone);

            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
