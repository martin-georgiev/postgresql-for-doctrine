<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * Marks a window function PostgreSQL lets IGNORE NULLS or RESPECT NULLS follow, which only lag(), lead(), first_value(), last_value() and nth_value() are.
 *
 * @since 4.10
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
interface NullTreatableWindowFunction extends WindowFunction {}
