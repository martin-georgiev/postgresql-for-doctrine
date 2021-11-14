<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

/**
 * @Entity
 */
class ContainsIntegers extends Entity
{
    /**
     * @var int
     *
     * @Column(type="integer")
     */
    public $integer1;

    /**
     * @var int
     *
     * @Column(type="integer")
     */
    public $integer2;

    /**
     * @var int
     *
     * @Column(type="integer")
     */
    public $integer3;
}
