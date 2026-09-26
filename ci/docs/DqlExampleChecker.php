<?php

declare(strict_types=1);

namespace MartinGeorgiev\Docs;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsArrays;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsBooleans;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsComposites;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsDates;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsGeometries;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsHstores;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsJsons;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsLtrees;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNetworks;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsNumerics;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsPoints;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsRanges;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsTexts;
use Fixtures\MartinGeorgiev\Doctrine\Entity\ContainsXml;

final readonly class DqlExampleChecker
{
    /**
     * @var array<string, class-string>
     */
    private const FIXTURE_ENTITY_BY_PLACEHOLDER_FIELD_KEYWORD = [
        'geom' => ContainsGeometries::class,
        'geog' => ContainsGeometries::class,
        'trajectory' => ContainsGeometries::class,
        'centroid' => ContainsGeometries::class,
        'blade' => ContainsGeometries::class,
        'json' => ContainsJsons::class,
        'xml' => ContainsXml::class,
        'content' => ContainsXml::class,
        'range' => ContainsRanges::class,
        'array' => ContainsArrays::class,
        'tags' => ContainsArrays::class,
        'aliasvalues' => ContainsArrays::class,
        'path' => ContainsLtrees::class,
        'ip' => ContainsNetworks::class,
        'cidr' => ContainsNetworks::class,
        'point' => ContainsPoints::class,
        'date' => ContainsDates::class,
        'time' => ContainsDates::class,
        'created' => ContainsDates::class,
        'day' => ContainsDates::class,
        'interval' => ContainsDates::class,
        'start' => ContainsDates::class,
        'end' => ContainsDates::class,
        'bool' => ContainsBooleans::class,
        'item' => ContainsComposites::class,
        'composite' => ContainsComposites::class,
        'data' => ContainsHstores::class,
        'integer' => ContainsNumerics::class,
        'decimal' => ContainsNumerics::class,
        'price' => ContainsNumerics::class,
        'amount' => ContainsNumerics::class,
        'score' => ContainsNumerics::class,
        'angle' => ContainsNumerics::class,
        'degrees' => ContainsNumerics::class,
        'value' => ContainsNumerics::class,
        'base' => ContainsNumerics::class,
        'exponent' => ContainsNumerics::class,
        'step' => ContainsNumerics::class,
        'stop' => ContainsNumerics::class,
        'x' => ContainsNumerics::class,
        'y' => ContainsNumerics::class,
        'row' => ContainsNumerics::class,
    ];

    private EntityManager $entityManager;

    /**
     * @param array<string, class-string<FunctionNode>> $functionClassByRegisteredName
     */
    public function __construct(
        Repository $repository,
        private array $functionClassByRegisteredName,
    ) {
        $configuration = ORMSetup::createAttributeMetadataConfiguration([
            __DIR__.'/Entity',
            $repository->rootDirectory.'/fixtures/MartinGeorgiev/Doctrine/Entity',
        ], true);
        foreach ($functionClassByRegisteredName as $name => $functionClass) {
            $configuration->addCustomStringFunction($name, $functionClass);
        }

        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $configuration);
        $this->entityManager = new EntityManager($connection, $configuration);
    }

    public static function run(): bool
    {
        $repository = new Repository();
        $dqlExampleCollector = new DqlExampleCollector($repository);
        $checker = new self($repository, (new SetupGuides($repository))->functionsRegisteredByTheDoctrineGuide());
        $docblockExamples = $dqlExampleCollector->docblockExamples();
        $dqlFenceStatements = $dqlExampleCollector->dqlFenceStatements();

        $failureCount = 0;
        foreach ([...$docblockExamples, ...$dqlFenceStatements] as $dqlExample) {
            foreach ($checker->failuresOf($dqlExample) as $failure) {
                $failureCount++;
                \fwrite(\STDERR, \sprintf("%s: %s\n    %s\n", $dqlExample->fileAndLine, $failure, \str_replace("\n", "\n    ", $dqlExample->dql)));
            }
        }

        \fwrite($failureCount === 0 ? \STDOUT : \STDERR, \sprintf(
            "%d @example docblocks and %d statements in ```dql fences checked, failures: %d.\n",
            \count($docblockExamples),
            \count($dqlFenceStatements),
            $failureCount
        ));

        return $failureCount === 0;
    }

    /**
     * @return list<string>
     */
    public function failuresOf(DqlExample $dqlExample): array
    {
        if ($dqlExample->documentedFunctionClass === null) {
            $parseError = $this->parseErrorOf($dqlExample->dql);

            return $parseError === null ? [] : ['does not parse: '.$parseError];
        }

        if ($dqlExample->dql === '') {
            return [\sprintf('the @example of %s holds no double-quoted DQL', $dqlExample->documentedFunctionClass)];
        }

        $failures = [];
        $parseError = $this->parseErrorOf($this->withPlaceholdersMappedToFixtures($dqlExample->dql));
        if ($parseError !== null) {
            $failures[] = \sprintf('the @example of %s does not parse: %s', $dqlExample->documentedFunctionClass, $parseError);
        }

        $registeredNames = \array_keys($this->functionClassByRegisteredName, $dqlExample->documentedFunctionClass, true);
        if (!$this->callsAnyOf($registeredNames, $dqlExample->dql)) {
            $failures[] = \sprintf(
                'the @example of %s calls none of the names %s registers it under [%s]',
                $dqlExample->documentedFunctionClass,
                SetupGuides::DOCTRINE,
                \implode(', ', $registeredNames)
            );
        }

        return $failures;
    }

    private function parseErrorOf(string $dql): ?string
    {
        try {
            $this->entityManager->createQuery($dql)->getSQL();
        } catch (\Throwable $throwable) {
            return \sprintf('%s: %s', $throwable::class, $throwable->getMessage());
        }

        return null;
    }

    /**
     * @param list<string> $functionNames
     */
    private function callsAnyOf(array $functionNames, string $dql): bool
    {
        foreach ($functionNames as $functionName) {
            if (\preg_match('/(?<![\w.])'.\preg_quote($functionName, '/').'\s*\(/i', $dql) === 1) {
                return true;
            }
        }

        return false;
    }

    private function withPlaceholdersMappedToFixtures(string $example): string
    {
        $withoutStringLiterals = (string) \preg_replace("/'(?:[^']|'')*'/", "''", $example);
        \preg_match_all('/(?<![\w:.])(?<alias>[a-z]\w*)\.(?<field>[A-Za-z_]\w*)/', $withoutStringLiterals, $fieldReferences, \PREG_SET_ORDER);

        $fieldsByAlias = [];
        foreach ($fieldReferences as $fieldReference) {
            $fieldsByAlias[$fieldReference['alias']][$fieldReference['field']] = true;
        }

        $dql = $example;
        $isAWhereClauseOnly = \preg_match('/^\s*WHERE\b/i', $dql) === 1;
        if ($isAWhereClauseOnly) {
            $aliases = \array_keys($fieldsByAlias) ?: ['e'];
            $from = \implode(', ', \array_map(static fn (string $alias): string => 'Entity '.$alias, $aliases));
            $dql = \sprintf('SELECT %s.id FROM %s %s', $aliases[0], $from, \trim($dql));
        }

        $fixtureEntityByAlias = [];
        foreach ($fieldsByAlias as $alias => $fields) {
            $fixtureEntity = $this->fixtureEntityNamedBy((string) (\array_key_first(\array_diff_key($fields, ['id' => true])) ?? 'text'));
            $fixtureEntityByAlias[$alias] = $fixtureEntity;

            $fixtureColumns = \array_values(\array_diff(\array_keys(\get_class_vars($fixtureEntity)), ['id']));
            $fixtureColumnByField = [];
            $columnIndex = 0;
            foreach (\array_keys($fields) as $field) {
                $fixtureColumnByField[$field] = $field === 'id' ? 'id' : $fixtureColumns[$columnIndex++ % \count($fixtureColumns)];
            }

            $dql = (string) \preg_replace_callback(
                '/(?<![\w:.])'.\preg_quote($alias, '/').'\.(?<field>[A-Za-z_]\w*)/',
                static fn (array $match): string => $alias.'.'.($fixtureColumnByField[$match['field']] ?? $match['field']),
                $dql
            );
        }

        return (string) \preg_replace_callback(
            '/\bEntity\s+(?<alias>[a-z]\w*)/',
            static fn (array $match): string => '\\'.($fixtureEntityByAlias[$match['alias']] ?? ContainsTexts::class).' '.$match['alias'],
            $dql
        );
    }

    /**
     * @return class-string
     */
    private function fixtureEntityNamedBy(string $placeholderField): string
    {
        foreach (self::FIXTURE_ENTITY_BY_PLACEHOLDER_FIELD_KEYWORD as $keyword => $fixtureEntity) {
            if (\str_contains(\strtolower($placeholderField), $keyword)) {
                return $fixtureEntity;
            }
        }

        return ContainsTexts::class;
    }
}
