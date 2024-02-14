<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsDates extends Entity
{
    #[ORM\Column(type: 'date_immutable')]
    public \DateTimeImmutable $date1;

    #[ORM\Column(type: 'date_immutable')]
    public \DateTimeImmutable $date2;
}
