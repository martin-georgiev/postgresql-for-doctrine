<?php

namespace MartinGeorgiev\Tests\Doctrine\Fixtures\Entity;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * @Entity
 */
class ContainsSeveralIntegers extends Entity
{
    /**
     * @Column(type="integer")
     */
    public $integer1;

    /**
     * @Column(type="integer")
     */
    public $integer2;

    /**
     * @Column(type="integer")
     */
    public $integer3;
}
