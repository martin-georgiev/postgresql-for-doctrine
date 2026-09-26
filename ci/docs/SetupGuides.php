<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\Docs;

use Ci\MartinGeorgiev\Shared\Repository;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

final readonly class SetupGuides
{
    /**
     * @var string
     */
    public const DOCTRINE = 'docs/INTEGRATING-WITH-DOCTRINE.md';

    /**
     * @var string
     */
    public const SYMFONY = 'docs/INTEGRATING-WITH-SYMFONY.md';

    /**
     * @var string
     */
    public const LARAVEL = 'docs/INTEGRATING-WITH-LARAVEL.md';

    /**
     * @var string
     */
    private const TYPE_CATALOGUE = 'docs/AVAILABLE-TYPES.md';

    /**
     * @var array<string, string>
     */
    private const TYPE_REGISTRATION_PATTERN_BY_FILE = [
        self::TYPE_CATALOGUE => '/\| %s \| /',
        self::DOCTRINE => "/addType\\('%s', /",
        self::SYMFONY => "/^ *'?%s'?: MartinGeorgiev/m",
        self::LARAVEL => "/'%s' => /",
    ];

    /**
     * @var array<string, string>
     */
    private const FUNCTION_REGISTRATION_PATTERN_BY_GUIDE = [
        self::DOCTRINE => "/addCustom(?:String|Numeric|Datetime)Function\\('(?<name>[^']+)', (?<class>MartinGeorgiev\\\\Doctrine\\\\ORM\\\\[\\w\\\\]+)::class\\)/",
        self::SYMFONY => "/^ *'?(?<name>\\w+)'?: (?<class>MartinGeorgiev\\\\Doctrine\\\\ORM\\\\[\\w\\\\]+)/m",
        self::LARAVEL => "/'(?<name>[^']+)' => (?<class>MartinGeorgiev\\\\Doctrine\\\\ORM\\\\[\\w\\\\]+)::class/",
    ];

    /**
     * @var string
     */
    private const DOCTRINE_TYPE_CLASS_REGISTRATION_PATTERN = "/addType\\('(?<name>[^']+)', \"(?<class>MartinGeorgiev[\\\\\\w]+)\"\\)/";

    public function __construct(
        private Repository $repository,
    ) {}

    /**
     * @return list<string>
     */
    public function filesListingEveryType(): array
    {
        return \array_keys(self::TYPE_REGISTRATION_PATTERN_BY_FILE);
    }

    /**
     * @param list<string> $typeNames
     *
     * @return list<string>
     */
    public function typeNamesNotRegisteredIn(string $file, array $typeNames): array
    {
        $contents = $this->repository->read($file);

        return \array_values(\array_filter(
            $typeNames,
            static fn (string $typeName): bool => \preg_match(\sprintf(self::TYPE_REGISTRATION_PATTERN_BY_FILE[$file], \preg_quote($typeName, '/')), $contents) !== 1
        ));
    }

    /**
     * @return list<string>
     */
    public function guidesRegisteringEveryFunction(): array
    {
        return \array_keys(self::FUNCTION_REGISTRATION_PATTERN_BY_GUIDE);
    }

    /**
     * @return list<array{name: string, class: string}> in the order the guide lists them, duplicates kept
     */
    public function functionRegistrationsIn(string $guide): array
    {
        \preg_match_all(self::FUNCTION_REGISTRATION_PATTERN_BY_GUIDE[$guide], $this->repository->read($guide), $matches, \PREG_SET_ORDER);

        return \array_map(static fn (array $match): array => ['name' => $match['name'], 'class' => $match['class']], $matches);
    }

    /**
     * @return array<string, class-string<FunctionNode>> DQL name => function class
     */
    public function functionsRegisteredByTheDoctrineGuide(): array
    {
        $functionClassByName = [];
        foreach ($this->functionRegistrationsIn(self::DOCTRINE) as $registration) {
            if (!\is_a($registration['class'], FunctionNode::class, true)) {
                throw new \RuntimeException(\sprintf('%s registers %s under %s, which is not a DQL function class.', self::DOCTRINE, $registration['class'], $registration['name']));
            }

            $functionClassByName[$registration['name']] = $registration['class'];
        }

        \ksort($functionClassByName);

        return $functionClassByName;
    }

    /**
     * @return array<string, class-string<DoctrineType>> type name => DBAL type class
     */
    public function typesRegisteredByTheDoctrineGuide(): array
    {
        \preg_match_all(self::DOCTRINE_TYPE_CLASS_REGISTRATION_PATTERN, $this->repository->read(self::DOCTRINE), $registrations, \PREG_SET_ORDER);

        $typeClassByName = [];
        foreach ($registrations as $registration) {
            $typeClass = \str_replace('\\\\', '\\', $registration['class']);
            if (!\is_a($typeClass, DoctrineType::class, true)) {
                throw new \RuntimeException(\sprintf('%s registers %s under %s, which is not a DBAL type class.', self::DOCTRINE, $typeClass, $registration['name']));
            }

            $typeClassByName[$registration['name']] = $typeClass;
        }

        return $typeClassByName;
    }
}
