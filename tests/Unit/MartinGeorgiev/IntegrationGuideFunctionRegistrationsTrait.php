<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev;

/**
 * Reads the DQL function registrations out of the integration guides, so the Doctrine guide is the one registry the
 * documentation tests register functions from.
 */
trait IntegrationGuideFunctionRegistrationsTrait
{
    /**
     * @var string
     */
    private const DOCTRINE_INTEGRATION_GUIDE = 'docs/INTEGRATING-WITH-DOCTRINE.md';

    /**
     * @var string
     */
    private const DOCTRINE_FUNCTION_REGISTRATION_PATTERN = "/addCustom(?:String|Numeric|Datetime)Function\\('(?<name>[^']+)', (?<class>MartinGeorgiev\\\\Doctrine\\\\ORM\\\\[\\w\\\\]+)::class\\)/";

    /**
     * @return list<array{name: string, class: string}> in the order the guide registers them, duplicates kept
     */
    private static function functionRegistrationsIn(string $documentationFile, string $registrationPattern): array
    {
        $documentation = \file_get_contents(__DIR__.'/../../../'.$documentationFile);
        if (!\is_string($documentation)) {
            throw new \RuntimeException(\sprintf('Could not read %s.', $documentationFile));
        }

        \preg_match_all($registrationPattern, $documentation, $matches, \PREG_SET_ORDER);

        return \array_map(static fn (array $match): array => ['name' => $match['name'], 'class' => $match['class']], $matches);
    }

    /**
     * @return array<string, string> DQL name => function class, as the Doctrine guide registers them
     */
    private static function functionsRegisteredByTheDoctrineGuide(): array
    {
        $functions = [];
        foreach (self::functionRegistrationsIn(self::DOCTRINE_INTEGRATION_GUIDE, self::DOCTRINE_FUNCTION_REGISTRATION_PATTERN) as $registration) {
            $functions[$registration['name']] = $registration['class'];
        }

        \ksort($functions);

        return $functions;
    }
}
