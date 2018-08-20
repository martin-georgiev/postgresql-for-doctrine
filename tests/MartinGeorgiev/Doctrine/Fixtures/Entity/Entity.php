<?php

namespace MartinGeorgiev\Tests\Doctrine\Fixtures\Entity;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class Entity
{
    /**
     * @Id
     * @Column(type="string")
     * @GeneratedValue
     */
    public $id;
}
