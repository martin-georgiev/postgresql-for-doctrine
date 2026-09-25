<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Marks a DQL function whose SQL is an aggregate call, the only kind PostgreSQL lets FILTER or OVER follow.
 *
 * @since 4.9
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
interface AggregateFunction {}
