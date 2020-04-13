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
class ContainsTexts extends Entity
{
    /**
     * @Column(type="text")
     */
    public $text1;

    /**
     * @Column(type="text")
     */
    public $text2;
}
