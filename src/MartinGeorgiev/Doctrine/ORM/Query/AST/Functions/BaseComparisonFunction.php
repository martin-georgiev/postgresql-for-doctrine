<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * @since 1.0
 *
 * @author Martin Georgiev <martin.georgiev@gmail.com>
 */
abstract class BaseComparisonFunction extends BaseVariadicFunction
{
    protected string $commonNodeMapping = 'ArithmeticPrimary';
}
