<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\DBAL\Types\ValueObject\Exceptions;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The DBAL types translate value object failures with catch (\InvalidArgumentException).
 * A member of this family extending anything else escapes that catch and surfaces
 * from the wrong layer, which no type-level test would notice.
 */
final class FamilyTest extends TestCase
{
    private const NAMESPACE = 'MartinGeorgiev\\Doctrine\\DBAL\\Types\\ValueObject\\Exceptions\\';

    /**
     * @param class-string $class
     */
    #[DataProvider('provideFamilyMembers')]
    #[Test]
    public function extends_the_translatable_parent(string $class): void
    {
        $this->assertTrue(\is_subclass_of($class, \InvalidArgumentException::class));
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('provideFamilyMembers')]
    #[Test]
    public function is_final(string $class): void
    {
        $this->assertTrue((new \ReflectionClass($class))->isFinal());
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('provideFamilyMembers')]
    #[Test]
    public function builds_every_instance_through_a_for_named_factory(string $class): void
    {
        $reflectionClass = new \ReflectionClass($class);
        $publicMethods = $reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC);

        $factories = \array_map(
            static fn (\ReflectionMethod $reflectionMethod): string => $reflectionMethod->getName(),
            \array_filter($publicMethods, static fn (\ReflectionMethod $reflectionMethod): bool => $reflectionMethod->isStatic() && $reflectionMethod->getDeclaringClass()->getName() === $class)
        );

        $this->assertNotEmpty($factories);
        foreach ($factories as $factory) {
            $this->assertStringStartsWith('for', $factory);
        }
    }

    /**
     * @return \Generator<string, array{class-string}>
     */
    public static function provideFamilyMembers(): \Generator
    {
        $directory = __DIR__.'/../../../../../../../../src/MartinGeorgiev/Doctrine/DBAL/Types/ValueObject/Exceptions';
        $files = \glob($directory.'/*.php');
        self::assertIsArray($files);
        self::assertNotEmpty($files);

        foreach ($files as $file) {
            $name = \basename($file, '.php');
            $class = self::NAMESPACE.$name;
            self::assertTrue(\class_exists($class));

            yield $name => [$class];
        }
    }
}
