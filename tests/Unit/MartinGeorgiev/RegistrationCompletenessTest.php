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
    private const ROOT_DIRECTORY = __DIR__.'/../../..';

    private const TYPE_DECLARATION_FILE = 'src/MartinGeorgiev/Doctrine/DBAL/Type.php';

    private const TYPE_REGISTRATION_FILE = 'tests/Integration/MartinGeorgiev/TestCase.php';

    private const FUNCTION_SOURCE_DIRECTORY = 'src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions';

    private const FUNCTION_SOURCE_NAMESPACE = 'MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\';

    private const FUNCTION_INTEGRATION_TEST_DIRECTORY = 'tests/Integration/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions';

    /**
     * Deptrac forbids the unit layer from depending on the integration one, so the integration
     * test cases are reached as data - discovered by path and reflected on - never imported.
     *
     * @var string
     */
    private const FUNCTION_INTEGRATION_TEST_NAMESPACE = 'Tests\\Integration\\MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\';

    /**
     * Subfolders holding building blocks rather than callable PostgreSQL functions.
     *
     * @var list<string>
     */
    private const NON_FUNCTION_SOURCE_SUBFOLDERS = ['Exception', 'Traits'];

    /**
     * The `<<#>>` operator was removed from PostGIS in 2.2.0, so none of the PostGIS versions in
     * the CI matrix can execute it and no integration test can cover it. The class is kept for
     * legacy compatibility only - see its own docblock.
     *
     * @var list<class-string>
     */
    private const FUNCTIONS_UNSUPPORTED_BY_EVERY_TESTED_POSTGIS = [
        NDimensionalBoundingBoxDistance::class,
    ];

    #[Test]
    public function registers_every_declared_type_with_the_integration_test_case(): void
    {
        $declaredTypeNames = $this->findDeclaredTypeNames();
        $registeredTypeNames = $this->findTypeNamesRegisteredForIntegrationTests();

        $unregistered = \array_values(\array_diff($declaredTypeNames, $registeredTypeNames));
        $this->assertSame([], $unregistered, \sprintf(
            'These type names are declared as constants in %s but are missing from the $typesMap in registerCustomTypes() in %s.',
            self::TYPE_DECLARATION_FILE,
            self::TYPE_REGISTRATION_FILE
        ));

        $undeclared = \array_values(\array_diff($registeredTypeNames, $declaredTypeNames));
        $this->assertSame([], $undeclared, \sprintf(
            'These type names are registered in the $typesMap in %s but are not declared as constants in %s.',
            self::TYPE_REGISTRATION_FILE,
            self::TYPE_DECLARATION_FILE
        ));
    }

    #[Test]
    public function registers_every_function_class_with_an_integration_test_case(): void
    {
        $unregisteredFunctionClasses = \array_values(\array_diff(
            $this->findConcreteFunctionClasses(),
            $this->findFunctionClassesRegisteredForIntegrationTests()
        ));

        $allowedToBeUnregistered = self::FUNCTIONS_UNSUPPORTED_BY_EVERY_TESTED_POSTGIS;
        \sort($allowedToBeUnregistered);

        $message = \sprintf(
            'Every concrete function class in %s must be registered in a getStringFunctions() of some test case in %s. Register the class there, or - when no tested PostgreSQL version can run it - add it to %s::FUNCTIONS_UNSUPPORTED_BY_EVERY_TESTED_POSTGIS.',
            self::FUNCTION_SOURCE_DIRECTORY,
            self::FUNCTION_INTEGRATION_TEST_DIRECTORY,
            self::class
        );
        $this->assertSame($allowedToBeUnregistered, $unregisteredFunctionClasses, $message);
    }

    #[DataProvider('provideDocumentationThatMustListEveryType')]
    #[Test]
    public function documents_every_declared_type(string $documentationFile, string $registrationPattern): void
    {
        $documentation = $this->readRepositoryFile($documentationFile);

        $undocumentedTypeNames = [];
        foreach ($this->findDeclaredTypeNames() as $typeName) {
            $pattern = \sprintf($registrationPattern, \preg_quote($typeName, '/'));
            if (\preg_match($pattern, $documentation) !== 1) {
                $undocumentedTypeNames[] = $typeName;
            }
        }

        $message = \sprintf(
            'Every type name declared as a constant in %s must be registered in %s.',
            self::TYPE_DECLARATION_FILE,
            $documentationFile
        );
        $this->assertSame([], $undocumentedTypeNames, $message);
    }

    /**
     * Each pattern carries the shape a registration takes in that file, so that a type merely
     * mentioned elsewhere - a mapping_types alias, a prose example - cannot stand in for one.
     * Symfony quotes only the names YAML would otherwise misread.
     *
     * @return array<string, array{documentationFile: string, registrationPattern: string}>
     */
    public static function provideDocumentationThatMustListEveryType(): array
    {
        return [
            'type catalogue' => [
                'documentationFile' => 'docs/AVAILABLE-TYPES.md',
                'registrationPattern' => '/\| %s \| /',
            ],
            'Doctrine integration guide' => [
                'documentationFile' => 'docs/INTEGRATING-WITH-DOCTRINE.md',
                'registrationPattern' => "/addType\\('%s', /",
            ],
            'Symfony integration guide' => [
                'documentationFile' => 'docs/INTEGRATING-WITH-SYMFONY.md',
                'registrationPattern' => "/^ *'?%s'?: MartinGeorgiev/m",
            ],
            'Laravel integration guide' => [
                'documentationFile' => 'docs/INTEGRATING-WITH-LARAVEL.md',
                'registrationPattern' => "/'%s' => /",
            ],
        ];
    }

    /**
     * @return list<string>
     */
    private function findDeclaredTypeNames(): array
    {
        $declaredConstants = (new \ReflectionClass(Type::class))->getConstants();
        $typeNames = \array_values(\array_filter($declaredConstants, \is_string(...)));
        $this->assertSameSize($declaredConstants, $typeNames, \sprintf(
            'Every constant in %s must name a PostgreSQL type as a string.',
            self::TYPE_DECLARATION_FILE
        ));

        \sort($typeNames);

        return $typeNames;
    }

    /**
     * @return list<string>
     */
    private function findTypeNamesRegisteredForIntegrationTests(): array
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
    private function findConcreteFunctionClasses(): array
    {
        $functionClasses = [];
        foreach ($this->findPhpFilesIn(self::FUNCTION_SOURCE_DIRECTORY) as $relativePath) {
            $subfolder = \strtok($relativePath, '/');
            if (\in_array($subfolder, self::NON_FUNCTION_SOURCE_SUBFOLDERS, true)) {
                continue;
            }

            $className = self::FUNCTION_SOURCE_NAMESPACE.\str_replace('/', '\\', \substr($relativePath, 0, -4));
            if (!\class_exists($className)) {
                continue;
            }

            if ((new \ReflectionClass($className))->isAbstract()) {
                continue;
            }

            $functionClasses[] = $className;
        }

        \sort($functionClasses);

        return $functionClasses;
    }

    /**
     * @return list<class-string>
     */
    private function findFunctionClassesRegisteredForIntegrationTests(): array
    {
        $functionClasses = [];
        foreach ($this->findPhpFilesIn(self::FUNCTION_INTEGRATION_TEST_DIRECTORY) as $relativePath) {
            $className = self::FUNCTION_INTEGRATION_TEST_NAMESPACE.\str_replace('/', '\\', \substr($relativePath, 0, -4));
            if (!\class_exists($className)) {
                continue;
            }

            $testCase = new \ReflectionClass($className);
            if ($testCase->isAbstract() || !$testCase->hasMethod('getStringFunctions')) {
                continue;
            }

            /** @var array<string, class-string> $registeredFunctionClasses */
            $registeredFunctionClasses = $testCase->getMethod('getStringFunctions')->invoke($testCase->newInstanceWithoutConstructor());
            foreach ($registeredFunctionClasses as $registeredFunctionClass) {
                $functionClasses[] = $registeredFunctionClass;
            }
        }

        $functionClasses = \array_values(\array_unique($functionClasses));
        \sort($functionClasses);

        return $functionClasses;
    }

    /**
     * @return list<string> paths relative to the given directory
     */
    private function findPhpFilesIn(string $relativeDirectory): array
    {
        $directory = self::ROOT_DIRECTORY.'/'.$relativeDirectory;
        $files = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if (!$file instanceof \SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }

            $files[] = \substr($file->getPathname(), \strlen($directory) + 1);
        }

        \sort($files);

        return $files;
    }

    private function readRepositoryFile(string $relativePath): string
    {
        $contents = \file_get_contents(self::ROOT_DIRECTORY.'/'.$relativePath);
        $this->assertIsString($contents, \sprintf('Could not read %s.', $relativePath));

        return $contents;
    }
}
