<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev;

use MartinGeorgiev\Doctrine\DBAL\Type;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RegistrationCompletenessTest extends TestCase
{
    /**
     * @var string
     */
    private const ROOT_DIRECTORY = __DIR__.'/../../..';

    /**
     * @var string
     */
    private const TYPE_DECLARATION_FILE = 'src/MartinGeorgiev/Doctrine/DBAL/Type.php';

    /**
     * @var string
     */
    private const TYPE_REGISTRATION_FILE = 'tests/Integration/MartinGeorgiev/TestCase.php';

    /**
     * @var string
     */
    private const FUNCTION_SOURCE_DIRECTORY = 'src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions';

    /**
     * @var string
     */
    private const FUNCTION_SOURCE_NAMESPACE = 'MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\';

    /**
     * @var string
     */
    private const FUNCTION_INTEGRATION_TEST_DIRECTORY = 'tests/Integration/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions';

    /**
     * Deptrac forbids the unit layer depending on the integration one, so these test cases are reached as data.
     *
     * @var string
     */
    private const FUNCTION_INTEGRATION_TEST_NAMESPACE = 'Tests\\Integration\\MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\';

    /**
     * Building blocks rather than callable PostgreSQL functions.
     *
     * @var list<string>
     */
    private const NON_FUNCTION_SOURCE_SUBFOLDERS = ['Exception', 'Traits'];

    #[Test]
    public function registers_every_declared_type_with_the_integration_test_case(): void
    {
        $this->assertSame($this->declaredTypeNames(), $this->typeNamesRegisteredForIntegrationTests(), \sprintf(
            'The type names declared as constants in %s and the $typesMap in registerCustomTypes() in %s must be the same set.',
            self::TYPE_DECLARATION_FILE,
            self::TYPE_REGISTRATION_FILE
        ));
    }

    #[Test]
    public function registers_every_function_class_with_an_integration_test_case(): void
    {
        $unregistered = \array_values(\array_diff(
            $this->concreteFunctionClasses(),
            $this->functionClassesRegisteredForIntegrationTests()
        ));

        $this->assertSame([], $unregistered, \sprintf(
            'Every concrete function class in %s must be registered in a getStringFunctions() of some test case in %s.',
            self::FUNCTION_SOURCE_DIRECTORY,
            self::FUNCTION_INTEGRATION_TEST_DIRECTORY
        ));
    }

    /**
     * @return list<string>
     */
    private function declaredTypeNames(): array
    {
        $typeNames = \array_values(\array_filter((new \ReflectionClass(Type::class))->getConstants(), \is_string(...)));
        \sort($typeNames);

        return $typeNames;
    }

    /**
     * @return list<string>
     */
    private function typeNamesRegisteredForIntegrationTests(): array
    {
        $source = $this->readRepositoryFile(self::TYPE_REGISTRATION_FILE);
        if (\preg_match('/\$typesMap = \[(?<entries>.*?)\n\s*\];/s', $source, $typesMap) !== 1) {
            $this->fail(\sprintf('Could not find the $typesMap array in %s.', self::TYPE_REGISTRATION_FILE));
        }

        \preg_match_all("/'(?<typeName>[^']+)' =>/", $typesMap['entries'], $entries);
        $typeNames = $entries['typeName'];
        \sort($typeNames);

        return $typeNames;
    }

    /**
     * @return list<class-string>
     */
    private function concreteFunctionClasses(): array
    {
        $functionClasses = [];
        foreach ($this->classesIn(self::FUNCTION_SOURCE_DIRECTORY, self::FUNCTION_SOURCE_NAMESPACE) as $functionClass) {
            $subfolder = \strtok(\substr($functionClass, \strlen(self::FUNCTION_SOURCE_NAMESPACE)), '\\');
            $isBuildingBlock = \in_array($subfolder, self::NON_FUNCTION_SOURCE_SUBFOLDERS, true);
            if (!$isBuildingBlock && !(new \ReflectionClass($functionClass))->isAbstract()) {
                $functionClasses[] = $functionClass;
            }
        }

        return $functionClasses;
    }

    /**
     * @return list<class-string>
     */
    private function functionClassesRegisteredForIntegrationTests(): array
    {
        $functionClasses = [];
        foreach ($this->classesIn(self::FUNCTION_INTEGRATION_TEST_DIRECTORY, self::FUNCTION_INTEGRATION_TEST_NAMESPACE) as $testCaseClass) {
            $testCase = new \ReflectionClass($testCaseClass);
            if ($testCase->isAbstract() || !$testCase->hasMethod('getStringFunctions')) {
                continue;
            }

            /** @var array<string, class-string> $registered */
            $registered = $testCase->getMethod('getStringFunctions')->invoke($testCase->newInstanceWithoutConstructor());
            $functionClasses = [...$functionClasses, ...\array_values($registered)];
        }

        $functionClasses = \array_values(\array_unique($functionClasses));
        \sort($functionClasses);

        return $functionClasses;
    }

    /**
     * @return list<class-string> sorted
     */
    private function classesIn(string $relativeDirectory, string $namespace): array
    {
        $directory = self::ROOT_DIRECTORY.'/'.$relativeDirectory;
        $classes = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (!$file instanceof \SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = \substr($file->getPathname(), \strlen($directory) + 1);
            $className = $namespace.\str_replace('/', '\\', \substr($relativePath, 0, -4));
            if (\class_exists($className)) {
                $classes[] = $className;
            }
        }

        \sort($classes);

        return $classes;
    }

    private function readRepositoryFile(string $relativePath): string
    {
        $contents = \file_get_contents(self::ROOT_DIRECTORY.'/'.$relativePath);
        $this->assertIsString($contents, \sprintf('Could not read %s.', $relativePath));

        return $contents;
    }
}
