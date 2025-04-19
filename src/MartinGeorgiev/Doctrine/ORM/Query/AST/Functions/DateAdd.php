<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Node;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\Traits\TimezoneValidationTrait;

/**
 * Implementation of PostgreSQL DATE_ADD().
 *
 * Adds an interval to a timestamp with time zone, computing times of day and daylight-savings
 * adjustments according to the time zone.
 *
 * @see https://www.postgresql.org/docs/16/functions-datetime.html
 * @since 3.1
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 *
 * @example Using it in DQL: "SELECT DATE_ADD(e.timestampWithTz, '1 day', 'Europe/Sofia') FROM Entity e"
 */
class DateAdd extends BaseVariadicFunction
{
    use TimezoneValidationTrait;

    protected function getNodeMappingPattern(): array
    {
        return ['StringPrimary'];
    }

    protected function getFunctionName(): string
    {
        return 'date_add';
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

        // Validate that the third parameter is a valid timezone if provided
        if (\count($arguments) === 3) {
            $this->validateTimezone($arguments[2], $this->getFunctionName());
        }
    }
}
