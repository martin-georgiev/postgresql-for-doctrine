<?php

declare(strict_types=1);

namespace Fixtures\MartinGeorgiev\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class ContainsIntegers extends Entity
{
    /**
     * @ORM\Column(type="integer")
     */
    public int $integer1;

    /**
     * @ORM\Column(type="integer")
     */
    public int $integer2;

    /**
     * @ORM\Column(type="integer")
     */
    public int $integer3;
}
