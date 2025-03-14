<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsTexts extends Entity
{
    #[ORM\Column(type: Types::TEXT)]
    public string $text1;

    #[ORM\Column(type: Types::TEXT)]
    public string $text2;
}
