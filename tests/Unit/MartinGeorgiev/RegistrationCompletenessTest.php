<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev;

use MartinGeorgiev\Doctrine\DBAL\Type;
use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\PostGIS\NDimensionalBoundingBoxDistance;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RegistrationCompletenessTest extends TestCase
{
    use IntegrationGuideFunctionRegistrationsTrait;

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

    /**
     * PostGIS removed `<<#>>` in 2.2.0, so no version in the CI matrix can execute it and no integration test can cover it.
     *
     * @var list<class-string>
     */
    private const FUNCTIONS_UNSUPPORTED_BY_EVERY_TESTED_POSTGIS = [
        NDimensionalBoundingBoxDistance::class,
    ];

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

        $this->assertSame(self::FUNCTIONS_UNSUPPORTED_BY_EVERY_TESTED_POSTGIS, $unregistered, \sprintf(
            'Every concrete function class in %s must be registered in a getStringFunctions() of some test case in %s. Register the class there, or - when no tested PostgreSQL version can run it - add it to %s::FUNCTIONS_UNSUPPORTED_BY_EVERY_TESTED_POSTGIS.',
            self::FUNCTION_SOURCE_DIRECTORY,
            self::FUNCTION_INTEGRATION_TEST_DIRECTORY,
            self::class
        ));
    }

    #[DataProvider('provideDocumentationThatMustListEveryType')]
    #[Test]
    public function documents_every_declared_type(string $documentationFile, string $registrationPattern): void
    {
        $documentation = $this->readRepositoryFile($documentationFile);
        $undocumented = \array_values(\array_filter(
            $this->declaredTypeNames(),
            static fn (string $typeName): bool => \preg_match(\sprintf($registrationPattern, \preg_quote($typeName, '/')), $documentation) !== 1
        ));

        $this->assertSame([], $undocumented, \sprintf(
            'Every type name declared as a constant in %s must be registered in %s.',
            self::TYPE_DECLARATION_FILE,
            $documentationFile
        ));
    }

    /**
     * Each pattern is the shape a registration takes in that file, so a passing mention - an alias, a prose example -
     * cannot stand in for one. Symfony quotes only the names YAML would otherwise misread.
     *
     * @return array<string, array{documentationFile: string, registrationPattern: string}>
     */
    public static function provideDocumentationThatMustListEveryType(): array
    {
        return [
            'type catalogue' => ['documentationFile' => 'docs/AVAILABLE-TYPES.md', 'registrationPattern' => '/\| %s \| /'],
            'Doctrine integration guide' => ['documentationFile' => 'docs/INTEGRATING-WITH-DOCTRINE.md', 'registrationPattern' => "/addType\\('%s', /"],
            'Symfony integration guide' => ['documentationFile' => 'docs/INTEGRATING-WITH-SYMFONY.md', 'registrationPattern' => "/^ *'?%s'?: MartinGeorgiev/m"],
            'Laravel integration guide' => ['documentationFile' => 'docs/INTEGRATING-WITH-LARAVEL.md', 'registrationPattern' => "/'%s' => /"],
        ];
    }

    #[DataProvider('provideDocumentationThatMustRegisterEveryFunction')]
    #[Test]
    public function documents_every_function_class(string $documentationFile, string $registrationPattern): void
    {
        $registrations = self::functionRegistrationsIn($documentationFile, $registrationPattern);
        $registrationCountByClass = \array_count_values(\array_column($registrations, 'class'));
        \ksort($registrationCountByClass);
        $namesRegisteredMoreThanOnce = \array_keys(\array_filter(
            \array_count_values(\array_column($registrations, 'name')),
            static fn (int $count): bool => $count > 1
        ));

        $this->assertSame(\array_fill_keys($this->concreteFunctionClasses(), 1), $registrationCountByClass, \sprintf(
            'Every concrete function class in %s must be registered exactly once in %s, and nothing else registered there.',
            self::FUNCTION_SOURCE_DIRECTORY,
            $documentationFile
        ));
        $this->assertSame([], $namesRegisteredMoreThanOnce, \sprintf(
            'Every DQL function name must be registered only once in %s.',
            $documentationFile
        ));
    }

    /**
     * Each pattern captures a DQL name and a class under `MartinGeorgiev\Doctrine\ORM\`, so the type registrations
     * in the same guides never pass for function registrations.
     *
     * @return array<string, array{documentationFile: string, registrationPattern: string}>
     */
    public static function provideDocumentationThatMustRegisterEveryFunction(): array
    {
        return [
            'Doctrine integration guide' => ['documentationFile' => self::DOCTRINE_INTEGRATION_GUIDE, 'registrationPattern' => self::DOCTRINE_FUNCTION_REGISTRATION_PATTERN],
            ...self::provideDocumentationThatMustMatchTheDoctrineGuide(),
        ];
    }

    #[DataProvider('provideDocumentationThatMustMatchTheDoctrineGuide')]
    #[Test]
    public function documents_the_function_names_the_doctrine_guide_registers(string $documentationFile, string $registrationPattern): void
    {
        $registeredFunctions = \array_column(self::functionRegistrationsIn($documentationFile, $registrationPattern), 'class', 'name');
        \ksort($registeredFunctions);

        $this->assertSame(self::functionsRegisteredByTheDoctrineGuide(), $registeredFunctions, \sprintf(
            '%s must register every function under the same DQL name as %s.',
            $documentationFile,
            self::DOCTRINE_INTEGRATION_GUIDE
        ));
    }

    /**
     * @return array<string, array{documentationFile: string, registrationPattern: string}>
     */
    public static function provideDocumentationThatMustMatchTheDoctrineGuide(): array
    {
        return [
            'Symfony integration guide' => ['documentationFile' => 'docs/INTEGRATING-WITH-SYMFONY.md', 'registrationPattern' => "/^ *'?(?<name>\\w+)'?: (?<class>MartinGeorgiev\\\\Doctrine\\\\ORM\\\\[\\w\\\\]+)/m"],
            'Laravel integration guide' => ['documentationFile' => 'docs/INTEGRATING-WITH-LARAVEL.md', 'registrationPattern' => "/'(?<name>[^']+)' => (?<class>MartinGeorgiev\\\\Doctrine\\\\ORM\\\\[\\w\\\\]+)::class/"],
        ];
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
