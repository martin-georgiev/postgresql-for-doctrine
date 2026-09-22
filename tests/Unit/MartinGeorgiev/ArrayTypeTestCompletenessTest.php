<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev;

use MartinGeorgiev\Doctrine\DBAL\Types\BaseArray;
use MartinGeorgiev\Doctrine\DBAL\Types\GeographyArray;
use MartinGeorgiev\Doctrine\DBAL\Types\GeometryArray;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ArrayTypeTestCompletenessTest extends TestCase
{
    /**
     * @var string
     */
    private const ROOT_DIRECTORY = __DIR__.'/../../..';

    /**
     * @var string
     */
    private const TYPE_SOURCE_DIRECTORY = 'src/MartinGeorgiev/Doctrine/DBAL/Types';

    /**
     * @var string
     */
    private const TYPE_SOURCE_NAMESPACE = 'MartinGeorgiev\\Doctrine\\DBAL\\Types\\';

    /**
     * @var string
     */
    private const UNIT_TEST_NAMESPACE = 'Tests\\Unit\\MartinGeorgiev\\Doctrine\\DBAL\\Types\\';

    /**
     * @var list<string>
     */
    private const REQUIRED_OF_EVERY_ARRAY_TYPE = [
        'has_name',
        'converts_to_database_value',
        'converts_to_php_value',
        'throws_exception_for_invalid_type_inputs',
        'validates_valid_array_item_for_database',
        'converts_null_item_to_php_value',
    ];

    /**
     * Both assert on a rejected item, which only a type that narrows BaseArray's accept-everything default can produce.
     *
     * @var list<string>
     */
    private const REQUIRED_WHEN_ITEMS_ARE_VALIDATED = [
        'validates_invalid_array_item_for_database',
        'throws_exception_for_invalid_database_value_inputs',
    ];

    /**
     * @var string
     */
    private const REQUIRED_WHEN_THE_ITEM_HOOK_REJECTS_A_NON_STRING = 'throws_exception_for_non_string_item_from_database';

    /**
     * @var string
     */
    private const VALID_TRANSFORMATIONS_PROVIDER = 'provideValidTransformations';

    /**
     * @var list<string>
     */
    private const REQUIRED_VALID_TRANSFORMATIONS_ROWS = ['null', 'empty array'];

    /**
     * PostgreSQL normalises WKT on the way out (`POINTZ(1 2 3)` reads back as `POINT Z(1 2 3)`), so one row cannot
     * spell both directions and these two tests carry a write-side and a read-side provider instead.
     *
     * @var list<class-string>
     */
    private const TYPES_WHOSE_WRITTEN_AND_READ_LITERALS_DIFFER = [
        GeographyArray::class,
        GeometryArray::class,
    ];

    #[Test]
    public function declares_a_unit_test_for_every_array_type(): void
    {
        $missing = [];
        foreach ($this->arrayTypes() as $type) {
            $testClass = $this->unitTestClassOf($type);
            if (!\class_exists($testClass)) {
                $missing[] = \sprintf('%s has no unit test at %s', $type->getName(), $testClass);
            }
        }

        $this->assertSame([], $missing, \sprintf(
            'Every concrete %s subclass is tested by a unit test class mirroring its name under %s.',
            BaseArray::class,
            self::UNIT_TEST_NAMESPACE
        ));
    }

    #[Test]
    public function declares_every_required_test_method_for_every_array_type(): void
    {
        $missing = [];
        foreach ($this->arrayTypesWithAUnitTest() as [$type, $testClass]) {
            foreach (self::REQUIRED_OF_EVERY_ARRAY_TYPE as $method) {
                $missing = [...$missing, ...$this->reportUnlessDeclared($testClass, $method, '')];
            }

            if ($this->validatesItsItems($type)) {
                foreach (self::REQUIRED_WHEN_ITEMS_ARE_VALIDATED as $method) {
                    $missing = [...$missing, ...$this->reportUnlessDeclared($testClass, $method, \sprintf(', as %s overrides isValidArrayItemForDatabase()', $type->getShortName()))];
                }
            }

            if ($this->itemHookRejectsANonString($type)) {
                $method = self::REQUIRED_WHEN_THE_ITEM_HOOK_REJECTS_A_NON_STRING;
                $missing = [...$missing, ...$this->reportUnlessDeclared($testClass, $method, \sprintf(', as %s::transformArrayItemForPHP() throws for a non-string item', $type->getShortName()))];
            }
        }

        $this->assertSame([], $missing, \sprintf(
            'Declare the method on the test class itself or on the family test case it extends. See the required-method table in %s.',
            '.ai-tools/rules/test-naming-patterns.md'
        ));
    }

    #[Test]
    public function covers_null_and_the_empty_array_in_every_valid_transformations_provider(): void
    {
        $missing = [];
        foreach ($this->arrayTypesWithAUnitTest() as [$type, $testClass]) {
            if (\in_array($type->getName(), self::TYPES_WHOSE_WRITTEN_AND_READ_LITERALS_DIFFER, true)) {
                continue;
            }

            if (!$testClass->hasMethod(self::VALID_TRANSFORMATIONS_PROVIDER)) {
                $missing[] = \sprintf('%s has no %s()', $testClass->getShortName(), self::VALID_TRANSFORMATIONS_PROVIDER);

                continue;
            }

            $rows = $this->rowNamesOf($testClass);
            foreach (self::REQUIRED_VALID_TRANSFORMATIONS_ROWS as $row) {
                if (!\in_array($row, $rows, true)) {
                    $missing[] = \sprintf("%s::%s() has no '%s' row", $testClass->getShortName(), self::VALID_TRANSFORMATIONS_PROVIDER, $row);
                }
            }
        }

        $this->assertSame([], $missing, \sprintf(
            "A '%s' row maps null to null and an '%s' row maps [] to '{}', so both directions of the round trip cover them.",
            ...self::REQUIRED_VALID_TRANSFORMATIONS_ROWS
        ));
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return list<string>
     */
    private function reportUnlessDeclared(\ReflectionClass $reflectionClass, string $method, string $reason): array
    {
        if ($reflectionClass->hasMethod($method)) {
            return [];
        }

        return [\sprintf('%s is missing %s()%s', $reflectionClass->getShortName(), $method, $reason)];
    }

    /**
     * @param \ReflectionClass<object> $reflectionClass
     *
     * @return list<string>
     */
    private function rowNamesOf(\ReflectionClass $reflectionClass): array
    {
        $rows = $reflectionClass->getMethod(self::VALID_TRANSFORMATIONS_PROVIDER)->invoke(null);
        $this->assertIsIterable($rows);

        $rowNames = [];
        foreach ($rows as $rowName => $_) {
            $rowNames[] = \is_string($rowName) ? $rowName : '';
        }

        return $rowNames;
    }

    /**
     * @param \ReflectionClass<BaseArray> $reflectionClass
     */
    private function validatesItsItems(\ReflectionClass $reflectionClass): bool
    {
        return $reflectionClass->getMethod('isValidArrayItemForDatabase')->getDeclaringClass()->getName() !== BaseArray::class;
    }

    /**
     * Probed rather than read off the signature: the numeric and JSON hooks take an already-decoded scalar as readily
     * as the literal token, so no non-string input reaches a throw there.
     *
     * @param \ReflectionClass<BaseArray> $reflectionClass
     */
    private function itemHookRejectsANonString(\ReflectionClass $reflectionClass): bool
    {
        try {
            $reflectionClass->newInstanceWithoutConstructor()->transformArrayItemForPHP(123);

            return false;
        } catch (\Throwable) {
            return true;
        }
    }

    /**
     * @return list<array{\ReflectionClass<BaseArray>, \ReflectionClass<object>}>
     */
    private function arrayTypesWithAUnitTest(): array
    {
        $tested = [];
        foreach ($this->arrayTypes() as $type) {
            $testClass = $this->unitTestClassOf($type);
            if (\class_exists($testClass)) {
                $tested[] = [$type, new \ReflectionClass($testClass)];
            }
        }

        return $tested;
    }

    /**
     * @param \ReflectionClass<BaseArray> $reflectionClass
     */
    private function unitTestClassOf(\ReflectionClass $reflectionClass): string
    {
        return self::UNIT_TEST_NAMESPACE.\substr($reflectionClass->getName(), \strlen(self::TYPE_SOURCE_NAMESPACE)).'Test';
    }

    /**
     * @return list<\ReflectionClass<BaseArray>>
     */
    private function arrayTypes(): array
    {
        $directory = self::ROOT_DIRECTORY.'/'.self::TYPE_SOURCE_DIRECTORY;
        $types = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (!$file instanceof \SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = \substr($file->getPathname(), \strlen($directory) + 1);
            $className = self::TYPE_SOURCE_NAMESPACE.\str_replace('/', '\\', \substr($relativePath, 0, -4));
            if (!\class_exists($className)) {
                continue;
            }

            $type = new \ReflectionClass($className);
            if (!$type->isAbstract() && $type->isSubclassOf(BaseArray::class)) {
                /* @var \ReflectionClass<BaseArray> $type */
                $types[] = $type;
            }
        }

        \usort($types, static fn (\ReflectionClass $left, \ReflectionClass $right): int => $left->getName() <=> $right->getName());

        return $types;
    }
}
