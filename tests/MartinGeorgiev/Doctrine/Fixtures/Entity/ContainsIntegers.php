<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsIntegers extends Entity
{
    /**
     * @Column(type="integer")
     */
    public int $integer1;

    /**
     * @Column(type="integer")
     */
    public int $integer2;

    /**
     * @Column(type="integer")
     */
    public int $integer3;
}
