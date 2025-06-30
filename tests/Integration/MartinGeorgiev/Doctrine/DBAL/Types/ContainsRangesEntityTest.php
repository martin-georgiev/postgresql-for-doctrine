<?php

declare(strict_types=1);

namespace Tests\Integration\MartinGeorgiev\Doctrine\DBAL\Types;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges;
use MartinGeorgiev\Doctrine\DBAL\Types\Int4Range as Int4RangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\Int8Range as Int8RangeType;
use MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Int4Range as Int4RangeValueObject;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\MartinGeorgiev\TestCase;

final class ContainsRangesEntityTest extends TestCase
{
    private EntityManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Type::hasType('int4range')) {
            Type::addType('int4range', Int4RangeType::class);
        }

        if (!Type::hasType('int8range')) {
            Type::addType('int8range', Int8RangeType::class);
        }

        $this->entityManager = $this->createEntityManager();

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->createSchema([$this->entityManager->getClassMetadata(ContainsRanges::class)]);
    }

    protected function tearDown(): void
    {
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema([$this->entityManager->getClassMetadata(ContainsRanges::class)]);

        parent::tearDown();
    }

    #[Test]
    public function can_persist_and_retrieve_entity_with_ranges(): void
    {
        $containsRanges = new ContainsRanges();

        $this->entityManager->persist($containsRanges);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $retrievedEntity = $this->entityManager->find(ContainsRanges::class, $containsRanges->id);

        self::assertInstanceOf(ContainsRanges::class, $retrievedEntity);
        self::assertEquals('[1,1000)', (string) $retrievedEntity->int4Range1);
        self::assertEquals('[0,2147483647)', (string) $retrievedEntity->int4Range2);
        self::assertEquals('[1,'.PHP_INT_MAX.')', (string) $retrievedEntity->int8Range1);
        self::assertEquals('['.PHP_INT_MIN.',0)', (string) $retrievedEntity->int8Range2);
    }

    #[Test]
    public function can_update_range_values(): void
    {
        $containsRanges = new ContainsRanges();

        $this->entityManager->persist($containsRanges);
        $this->entityManager->flush();

        // Update the range
        $containsRanges->int4Range1 = new Int4RangeValueObject(100, 200);

        $this->entityManager->flush();
        $this->entityManager->clear();

        $retrievedEntity = $this->entityManager->find(ContainsRanges::class, $containsRanges->id);

        self::assertEquals('[100,200)', (string) $retrievedEntity->int4Range1);
    }

    #[Test]
    public function can_handle_null_ranges(): void
    {
        $containsRanges = new ContainsRanges();
        $containsRanges->int4Range1 = null;

        $this->entityManager->persist($containsRanges);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $retrievedEntity = $this->entityManager->find(ContainsRanges::class, $containsRanges->id);

        self::assertNull($retrievedEntity->int4Range1);
    }
}
