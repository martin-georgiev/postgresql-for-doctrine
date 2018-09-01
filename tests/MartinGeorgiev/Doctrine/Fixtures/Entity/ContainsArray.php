<?php

declare(strict_types=1);

namespace MartinGeorgiev\Tests\Doctrine\Fixtures\Entity;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * @Entity
 */
class ContainsArray extends Entity
{
    /**
     * @Column(type="array")
     */
    public $array;

    /**
     * @Column(type="array")
     */
    public $anotherArray;
}
