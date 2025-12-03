<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Literal;
use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Exception\InvalidTruncFieldException;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\TimezoneValidationTrait;

/**
 * Implementation of PostgreSQL DATE_TRUNC().
 *
 * @see https://www.postgresql.org/docs/current/functions-datetime.html#FUNCTIONS-DATETIME-TRUNC
 * @since 3.7
 *
 * @author Jan Klan <jan@klan.com.au>
 *
 * @example Using it in DQL: "SELECT DATE_TRUNC('day', e.timestampWithTz, 'Australia/Adelaide') FROM Entity e"
 */
class DateTrunc extends BaseVariadicFunction
{
    use TimezoneValidationTrait;
    /**
     * @see https://www.postgresql.org/docs/current/functions-datetime.html#FUNCTIONS-DATETIME-TRUNC
     */
    public const FIELDS = [
        'microseconds',
        'milliseconds',
        'second',
        'minute',
        'hour',
        'day',
        'week',
        'month',
        'quarter',
        'year',
        'decade',
        'century',
        'millennium',
    ];

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'date_trunc';
    }

    protected function getMinArgumentCount(): int
    {
        return 2;
    }

    protected function getMaxArgumentCount(): int
    {
        return 3;
    }

    protected function validateArguments(Node ...$arguments): void
    {
        parent::validateArguments(...$arguments);

        $this->validateTruncField($arguments[0]);

        // Validate that the third parameter is a valid timezone if provided
        if (\count($arguments) === 3) {
            $this->validateTimezone($arguments[2], $this->getFunctionName());
        }
    }

    /**
     * Validates that the given node represents a valid trunc field value.
     *
     * @throws InvalidTruncFieldException If the field value is invalid
     */
    protected function validateTruncField(Node $node): void
    {
        if (!$node instanceof Literal || !\is_string($node->value)) {
            throw InvalidTruncFieldException::forNonLiteralNode($node::class, $this->getFunctionName());
        }

        $field = \strtolower(\trim((string) $node->value, "'\""));

        if (!\in_array($field, self::FIELDS, true)) {
            throw InvalidTruncFieldException::forInvalidField($field, $this->getFunctionName());
        }
    }
}
