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
class ContainsTexts extends Entity
{
    /**
     * @var string
     *
     * @Column(type="text")
     */
    public $text1;

    /**
     * @var string
     *
     * @Column(type="text")
     */
    public $text2;
}
