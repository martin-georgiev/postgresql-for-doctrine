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
class ContainsJson extends Entity
{
    /**
     * @Column(type="json")
     */
    public $object;
}
