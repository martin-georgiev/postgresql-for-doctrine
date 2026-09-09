<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use MartinGeorgiev\Doctrine\DBAL\Types\Enum;

final class ConcreteTrickyLabelType extends Enum
{
    protected const TYPE_NAME = 'test_tricky_label';

    protected function getEnumClass(): string
    {
        return TrickyLabels::class;
    }
}
