<?php

declare(strict_types=1);

namespace Tests\MartinGeorgiev\Doctrine\Fixtures\Entity;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\TestCase as BaseTestCase;

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
