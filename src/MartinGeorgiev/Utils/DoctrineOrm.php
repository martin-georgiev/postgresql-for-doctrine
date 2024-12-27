<?php

declare(strict_types=1);

namespace MartinGeorgiev\Utils;

use Doctrine\ORM\Query\TokenType;

/**
 * @internal
 */
final class DoctrineOrm
{
    public static function isPre219(): bool
    {
        return !\class_exists(TokenType::class);
    }
}
