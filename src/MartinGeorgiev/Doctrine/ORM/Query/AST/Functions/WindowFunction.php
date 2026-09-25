<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Marks a DQL function whose SQL is a window-only call such as row_number(), which PostgreSQL accepts only before OVER.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
interface WindowFunction {}
