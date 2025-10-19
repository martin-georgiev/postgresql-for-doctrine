<?php

declare(strict_types=1);

namespace MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

/**
 * @since 3.2
 *
 * @author Jan Klan <jan@klan.com.au>
 */
abstract class BaseArithmeticFunction extends BaseVariadicFunction
{
    protected function getNodeMappingPattern(): array
    {
        return ['ArithmeticPrimary'];
    }

    protected function getMinArgumentCount(): int
    {
        return 1;
    }

    protected function getMaxArgumentCount(): int
    {
        return 1;
    }
}
