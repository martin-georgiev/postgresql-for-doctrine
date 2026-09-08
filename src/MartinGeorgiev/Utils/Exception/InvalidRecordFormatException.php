<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils\Exception;

/**
 * @since 4.8
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
class InvalidRecordFormatException extends \InvalidArgumentException
{
    public static function invalidFormat(string $details = ''): self
    {
        $message = 'Invalid record format';
        if ($details !== '') {
            $message .= ': '.$details;
        }

        return new self($message);
    }
}
