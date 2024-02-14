<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ContainsTexts extends Entity
{
    #[ORM\Column(type: 'text')]
    public string $text1;

    #[ORM\Column(type: 'text')]
    public string $text2;
}
