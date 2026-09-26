<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\TestSuite;

use Ci\MartinGeorgiev\Shared\Repository;
use MartinGeorgiev\Doctrine\DBAL\Type;

final readonly class IntegrationTestRegistrationChecker
{
    /**
     * @var string
     */
    private const TYPE_REGISTRATION_FILE = 'tests/Integration/MartinGeorgiev/TestCase.php';

    /**
     * @var string
     */
    private const FUNCTION_INTEGRATION_TEST_DIRECTORY = 'tests/Integration/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions';

    /**
     * @var string
     */
    private const FUNCTION_INTEGRATION_TEST_NAMESPACE = 'Tests\\Integration\\MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\';

    private function __construct(
        private Repository $repository,
    ) {}

    public static function run(): bool
    {
        $failures = (new self(new Repository()))->failures();
        foreach ($failures as $failure) {
            \fwrite(\STDERR, $failure."\n");
        }

        \fwrite($failures === [] ? \STDOUT : \STDERR, \sprintf("Type and function registrations in the integration tests checked, failures: %d.\n", \count($failures)));

        return $failures === [];
    }

    /**
     * @return list<string>
     */
    private function failures(): array
    {
        return [
            ...$this->typeRegistrationMismatches(),
            ...$this->functionClassesWithoutAnIntegrationTest(),
        ];
    }

    /**
     * @return list<string>
     */
    private function typeRegistrationMismatches(): array
    {
        $declaredTypeNames = \array_values(\array_filter((new \ReflectionClass(Type::class))->getConstants(), \is_string(...)));
        $registeredTypeNames = $this->typeNamesInTheIntegrationTypesMap();

        $failures = [];
        foreach (\array_diff($declaredTypeNames, $registeredTypeNames) as $typeName) {
            $failures[] = \sprintf('%s: $typesMap does not register the type %s, which %s declares', self::TYPE_REGISTRATION_FILE, $typeName, Type::class);
        }

        foreach (\array_diff($registeredTypeNames, $declaredTypeNames) as $typeName) {
            $failures[] = \sprintf('%s: $typesMap registers the type %s, which %s does not declare', self::TYPE_REGISTRATION_FILE, $typeName, Type::class);
        }

        return $failures;
    }

    /**
     * @return list<string>
     */
    private function typeNamesInTheIntegrationTypesMap(): array
    {
        $source = $this->repository->read(self::TYPE_REGISTRATION_FILE);
        if (\preg_match('/\$typesMap = \[(?<entries>.*?)\n\s*\];/s', $source, $typesMap) !== 1) {
            throw new \RuntimeException(\sprintf('Could not find the $typesMap array in %s.', self::TYPE_REGISTRATION_FILE));
        }

        \preg_match_all("/'(?<typeName>[^']+)' =>/", $typesMap['entries'], $entries);

        return $entries['typeName'];
    }

    /**
     * @return list<string>
     */
    private function functionClassesWithoutAnIntegrationTest(): array
    {
        $functionClassesWithAnIntegrationTest = $this->functionClassesRegisteredByIntegrationTests();

        $failures = [];
        foreach ($this->repository->concreteFunctionClasses() as $functionClass) {
            if (!\in_array($functionClass, $functionClassesWithAnIntegrationTest, true)) {
                $failures[] = \sprintf('%s: no getStringFunctions() in %s registers it', $functionClass, self::FUNCTION_INTEGRATION_TEST_DIRECTORY);
            }
        }

        return $failures;
    }

    /**
     * @return list<string>
     */
    private function functionClassesRegisteredByIntegrationTests(): array
    {
        $functionClasses = [];
        foreach ($this->functionIntegrationTestClasses() as $testCaseClass) {
            $testCase = new \ReflectionClass($testCaseClass);
            if ($testCase->isAbstract() || !$testCase->hasMethod('getStringFunctions')) {
                continue;
            }

            $registeredFunctions = $testCase->getMethod('getStringFunctions')->invoke($testCase->newInstanceWithoutConstructor());
            if (!\is_array($registeredFunctions)) {
                throw new \RuntimeException(\sprintf('%s::getStringFunctions() does not return an array.', $testCaseClass));
            }

            foreach ($registeredFunctions as $registeredFunction) {
                if (\is_string($registeredFunction)) {
                    $functionClasses[] = $registeredFunction;
                }
            }
        }

        return \array_values(\array_unique($functionClasses));
    }

    /**
     * @return list<class-string>
     */
    private function functionIntegrationTestClasses(): array
    {
        $testClasses = [];
        foreach ($this->repository->phpFilesIn(self::FUNCTION_INTEGRATION_TEST_DIRECTORY) as $relativePath) {
            $pathInTestDirectory = \substr($relativePath, \strlen(self::FUNCTION_INTEGRATION_TEST_DIRECTORY) + 1, -4);
            $testClass = self::FUNCTION_INTEGRATION_TEST_NAMESPACE.\str_replace('/', '\\', $pathInTestDirectory);
            if (\class_exists($testClass)) {
                $testClasses[] = $testClass;
            }
        }

        return $testClasses;
    }
}
