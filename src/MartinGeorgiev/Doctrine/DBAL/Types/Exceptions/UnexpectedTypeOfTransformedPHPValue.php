<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\DBAL\Types\Exceptions;

/**
 * @since 1.5
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class UnexpectedTypeOfTransformedPHPValue extends TypeException
{
    public function __construct(string $untransformedValue, string $typeOfTransformedPHPValue)
    {
        $message = \sprintf(
            'Transforming a PostgreSQL value "%s" results to an unexpected PHP value from type "%s".',
            $untransformedValue,
            $typeOfTransformedPHPValue
        );
        parent::__construct($message);
    }
}
