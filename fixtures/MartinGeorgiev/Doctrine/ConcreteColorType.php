<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Fixtures\MartinGeorgiev\Doctrine\Entity\Enum;
use Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\TestColor;

#[ORM\Entity()]
final class ConcreteColorType extends Enum
{
    protected const TYPE_NAME = 'test_color';

    protected function getEnumClass(): string
    {
        return TestColor::class;
    }
}
