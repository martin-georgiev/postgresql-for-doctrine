<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

abstract class Entity
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(type="string")
     *
     * @ORM\GeneratedValue
     */
    #[ORM\Id()]
    #[ORM\Column(type: 'string')]
    #[ORM\GeneratedValue()]
    public string $id;
}
