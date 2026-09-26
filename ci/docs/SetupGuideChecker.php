<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\Docs;

use Ci\MartinGeorgiev\Shared\Repository;
use MartinGeorgiev\Doctrine\DBAL\Type;

final readonly class SetupGuideChecker
{
    public function __construct(
        private Repository $repository,
        private SetupGuides $setupGuides,
    ) {}

    public static function run(): bool
    {
        $repository = new Repository();
        $failures = (new self($repository, new SetupGuides($repository)))->failures();
        foreach ($failures as $failure) {
            \fwrite(\STDERR, $failure."\n");
        }

        \fwrite($failures === [] ? \STDOUT : \STDERR, \sprintf("Type and function registrations in the setup guides checked, failures: %d.\n", \count($failures)));

        return $failures === [];
    }

    /**
     * @return list<string>
     */
    private function failures(): array
    {
        return [
            ...$this->undocumentedTypes(),
            ...$this->functionClassesNotRegisteredExactlyOnce(),
            ...$this->functionNamesRegisteredMoreThanOnce(),
            ...$this->registrationsDifferingFromTheDoctrineGuide(),
        ];
    }

    /**
     * @return list<string>
     */
    private function undocumentedTypes(): array
    {
        $declaredTypeNames = \array_values(\array_filter((new \ReflectionClass(Type::class))->getConstants(), \is_string(...)));

        $failures = [];
        foreach ($this->setupGuides->filesListingEveryType() as $file) {
            foreach ($this->setupGuides->typeNamesNotRegisteredIn($file, $declaredTypeNames) as $typeName) {
                $failures[] = \sprintf('%s: does not register the type %s', $file, $typeName);
            }
        }

        return $failures;
    }

    /**
     * @return list<string>
     */
    private function functionClassesNotRegisteredExactlyOnce(): array
    {
        $concreteFunctionClasses = $this->repository->concreteFunctionClasses();

        $failures = [];
        foreach ($this->setupGuides->guidesRegisteringEveryFunction() as $guide) {
            $registrationCountByClass = \array_count_values(\array_column($this->setupGuides->functionRegistrationsIn($guide), 'class'));
            foreach ($concreteFunctionClasses as $concreteFunctionClass) {
                $registrationCount = $registrationCountByClass[$concreteFunctionClass] ?? 0;
                if ($registrationCount !== 1) {
                    $failures[] = \sprintf('%s: registers %s %d times, not once', $guide, $concreteFunctionClass, $registrationCount);
                }
            }

            foreach (\array_diff(\array_keys($registrationCountByClass), $concreteFunctionClasses) as $unknownClass) {
                $failures[] = \sprintf('%s: registers %s, which is not a concrete function class', $guide, $unknownClass);
            }
        }

        return $failures;
    }

    /**
     * @return list<string>
     */
    private function functionNamesRegisteredMoreThanOnce(): array
    {
        $failures = [];
        foreach ($this->setupGuides->guidesRegisteringEveryFunction() as $guide) {
            $registrationCountByName = \array_count_values(\array_column($this->setupGuides->functionRegistrationsIn($guide), 'name'));
            foreach ($registrationCountByName as $name => $registrationCount) {
                if ($registrationCount > 1) {
                    $failures[] = \sprintf('%s: registers the DQL name %s %d times', $guide, $name, $registrationCount);
                }
            }
        }

        return $failures;
    }

    /**
     * @return list<string>
     */
    private function registrationsDifferingFromTheDoctrineGuide(): array
    {
        $doctrineRegistrations = \array_column($this->setupGuides->functionRegistrationsIn(SetupGuides::DOCTRINE), 'class', 'name');

        $failures = [];
        foreach ([SetupGuides::SYMFONY, SetupGuides::LARAVEL] as $guide) {
            $guideRegistrations = \array_column($this->setupGuides->functionRegistrationsIn($guide), 'class', 'name');
            foreach (\array_diff_assoc($doctrineRegistrations, $guideRegistrations) as $name => $functionClass) {
                $failures[] = \sprintf('%s: does not register %s as %s, which %s does', $guide, $functionClass, $name, SetupGuides::DOCTRINE);
            }

            foreach (\array_diff_assoc($guideRegistrations, $doctrineRegistrations) as $name => $functionClass) {
                $failures[] = \sprintf('%s: registers %s as %s, which %s does not', $guide, $functionClass, $name, SetupGuides::DOCTRINE);
            }
        }

        return $failures;
    }
}
