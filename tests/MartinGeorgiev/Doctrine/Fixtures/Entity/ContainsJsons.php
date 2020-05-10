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
class ContainsJsons extends Entity
{
    /**
     * @var array
     *
     * @Column(type="json")
     */
    public $object1;

    /**
     * @var array
     *
     * @Column(type="json")
     */
    public $object2;
}
