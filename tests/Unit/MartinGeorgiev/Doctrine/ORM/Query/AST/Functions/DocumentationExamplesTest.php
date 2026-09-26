<?php

declare(strict_types=1);

namespace Tests\Unit\MartinGeorgiev\Doctrine\ORM\Query\AST\Functions;

use App\Entity\Order;
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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\MartinGeorgiev\IntegrationGuideFunctionRegistrationsTrait;

/**
 * Parses the DQL the documentation shows: every `@example "<DQL>"` in the function docblocks and every ```dql fence in
 * docs/*.md and README.md. Functions are registered only under the names docs/INTEGRATING-WITH-DOCTRINE.md publishes,
 * so an example calling a name no reader can register fails too.
 */
final class DocumentationExamplesTest extends TestCase
{
    use IntegrationGuideFunctionRegistrationsTrait;

    /**
     * @var string
     */
    private const ROOT_DIRECTORY = __DIR__.'/../../../../../../../..';

    /**
     * @var string
     */
    private const FUNCTION_SOURCE_DIRECTORY = 'src/MartinGeorgiev/Doctrine/ORM/Query/AST/Functions';

    /**
     * @var string
     */
    private const FUNCTION_SOURCE_NAMESPACE = 'MartinGeorgiev\\Doctrine\\ORM\\Query\\AST\\Functions\\';

    /**
     * PHPUnit fails a test whose provider yields nothing, and until the docs label their DQL `dql` there is nothing to yield.
     *
     * @var string
     */
    private const NO_DQL_FENCE = '';

    /**
     * The docblock examples query a generic `Entity e` and placeholder fields such as `e.col`. For the parse only, each
     * alias becomes the fixture entity its first field names by the first keyword below that the field contains, and
     * each placeholder field one of that entity's columns.
     *
     * @var array<string, class-string>
     */
    private const FIXTURE_ENTITY_BY_FIELD_KEYWORD = [
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

    protected function getStringFunctions(): array
    {
        return self::functionsRegisteredByTheDoctrineGuide();
    }

    protected function getExpectedSqlStatements(): array
    {
        return [
            'a function under the name the Doctrine guide registers' => "SELECT ARRAY['foo', 'bar'] AS sclr_0 FROM ContainsArrays c0_",
            'the entities the documentation queries' => 'SELECT o0_.reference AS reference_0, c1_.name AS name_1 FROM orders o0_ INNER JOIN Customer c1_ ON o0_.customer_id = c1_.id',
        ];
    }

    protected function getDqlStatements(): array
    {
        return [
            'a function under the name the Doctrine guide registers' => \sprintf("SELECT ARRAY('foo', 'bar') FROM %s e", ContainsArrays::class),
            'the entities the documentation queries' => \sprintf('SELECT o.reference, c.name FROM %s o JOIN o.customer c', Order::class),
        ];
    }

    #[DataProvider('provideDocblockExamples')]
    #[Test]
    public function parses_the_docblock_example(string $functionClass, string $example): void
    {
        $this->assertNotSame('', $example, \sprintf('The @example of %s holds no double-quoted DQL.', $functionClass));

        $this->assertDqlParses($this->toParseableDql($example), \sprintf("The @example of %s does not parse.\n  example: %s", $functionClass, $example));
    }

    #[DataProvider('provideDocblockExamples')]
    #[Test]
    public function calls_its_own_function_by_a_documented_name(string $functionClass, string $example): void
    {
        $documentedNames = \array_keys(\array_filter(
            self::functionsRegisteredByTheDoctrineGuide(),
            static fn (string $registeredClass): bool => $registeredClass === $functionClass
        ));
        $callsItsOwnFunction = \array_filter(
            $documentedNames,
            static fn (string $name): bool => \preg_match('/(?<![\w.])'.\preg_quote($name, '/').'\s*\(/i', $example) === 1
        ) !== [];

        $this->assertTrue($callsItsOwnFunction, \sprintf(
            'The @example of %s calls none of the names %s registers it under [%s]: %s',
            $functionClass,
            self::DOCTRINE_INTEGRATION_GUIDE,
            \implode(', ', $documentedNames),
            $example
        ));
    }

    /**
     * Keyed by the source file and line of the `@example`, relative to the repository root.
     *
     * @return array<string, array{functionClass: string, example: string}>
     */
    public static function provideDocblockExamples(): array
    {
        $directory = self::ROOT_DIRECTORY.'/'.self::FUNCTION_SOURCE_DIRECTORY;
        $examples = [];
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (!$file instanceof \SplFileInfo || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = \substr($file->getPathname(), \strlen($directory) + 1);
            $functionClass = self::FUNCTION_SOURCE_NAMESPACE.\str_replace('/', '\\', \substr($relativePath, 0, -4));
            foreach (self::readLines($file->getPathname()) as $index => $line) {
                if (!\str_contains($line, '@example')) {
                    continue;
                }

                $example = \preg_match('/@example[^"]*"(?<dql>.*)"/', $line, $match) === 1 ? \str_replace('\"', '"', $match['dql']) : '';
                $examples[self::FUNCTION_SOURCE_DIRECTORY.'/'.$relativePath.':'.($index + 1)] = ['functionClass' => $functionClass, 'example' => $example];
            }
        }

        \ksort($examples);

        return $examples;
    }

    #[DataProvider('provideDqlFences')]
    #[Test]
    public function parses_the_dql_fence(string $dql): void
    {
        if ($dql === self::NO_DQL_FENCE) {
            $this->markTestSkipped('No ```dql fence in docs/*.md or README.md yet; the docs still label their DQL `sql`.');
        }

        $this->assertDqlParses($dql, 'The ```dql fence does not parse.');
    }

    /**
     * One row per statement: a fence holds one statement per paragraph, and `--` lines caption them as they do in SQL.
     * Keyed by the file and the line the statement starts on, relative to the repository root.
     *
     * @return array<string, array{dql: string}>
     */
    public static function provideDqlFences(): array
    {
        $documentationFiles = \array_map(
            static fn (string $path): string => \substr($path, \strlen(self::ROOT_DIRECTORY) + 1),
            [...(\glob(self::ROOT_DIRECTORY.'/docs/*.md') ?: []), self::ROOT_DIRECTORY.'/README.md']
        );

        $statements = [];
        foreach ($documentationFiles as $documentationFile) {
            $isInsideADqlFence = false;
            $statementStart = null;
            $statementLines = [];
            foreach (self::readLines(self::ROOT_DIRECTORY.'/'.$documentationFile) as $index => $line) {
                $endsAStatement = $isInsideADqlFence && (\trim($line) === '' || \preg_match('/^\s*```\s*$/', $line) === 1);
                if ($endsAStatement && $statementLines !== []) {
                    $statements[$documentationFile.':'.$statementStart] = ['dql' => \implode("\n", $statementLines)];
                    $statementLines = [];
                }

                if (\preg_match('/^\s*```(?<language>\w*)\s*$/', $line, $fence) === 1) {
                    $isInsideADqlFence = !$isInsideADqlFence && $fence['language'] === 'dql';

                    continue;
                }

                $isAStatementLine = $isInsideADqlFence && \trim($line) !== '' && !\str_starts_with(\ltrim($line), '--');
                if ($isAStatementLine) {
                    if ($statementLines === []) {
                        $statementStart = $index + 1;
                    }

                    $statementLines[] = \rtrim($line);
                }
            }

            if ($statementLines !== []) {
                $statements[$documentationFile.':'.$statementStart] = ['dql' => \implode("\n", $statementLines)];
            }
        }

        return $statements === [] ? ['no dql fence yet' => ['dql' => self::NO_DQL_FENCE]] : $statements;
    }

    private function assertDqlParses(string $dql, string $failureMessage): void
    {
        try {
            $sql = $this->buildEntityManager()->createQuery($dql)->getSQL();
        } catch (\Throwable $throwable) {
            $this->fail(\sprintf("%s\n  parsed:  %s\n  error:   %s: %s", $failureMessage, $dql, $throwable::class, $throwable->getMessage()));
        }

        $this->assertNotSame('', $sql);
    }

    /**
     * @return list<string>
     */
    private static function readLines(string $path): array
    {
        $lines = \file($path, \FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            throw new \RuntimeException(\sprintf('Could not read %s.', $path));
        }

        return $lines;
    }

    private function toParseableDql(string $example): string
    {
        $withoutStringLiterals = (string) \preg_replace("/'(?:[^']|'')*'/", "''", $example);
        \preg_match_all('/(?<![\w:.])(?<alias>[a-z]\w*)\.(?<field>[A-Za-z_]\w*)/', $withoutStringLiterals, $references, \PREG_SET_ORDER);

        $fieldsByAlias = [];
        foreach ($references as $reference) {
            $fieldsByAlias[$reference['alias']][$reference['field']] = true;
        }

        $dql = $example;
        $isAWhereClauseOnly = \preg_match('/^\s*WHERE\b/i', $dql) === 1;
        if ($isAWhereClauseOnly) {
            $aliases = \array_keys($fieldsByAlias) ?: ['e'];
            $from = \implode(', ', \array_map(static fn (string $alias): string => 'Entity '.$alias, $aliases));
            $dql = \sprintf('SELECT %s.id FROM %s %s', $aliases[0], $from, \trim($dql));
        }

        $entityByAlias = [];
        foreach ($fieldsByAlias as $alias => $fields) {
            $firstField = \strtolower((string) (\array_key_first(\array_diff_key($fields, ['id' => true])) ?? 'text'));
            $entityClass = ContainsTexts::class;
            foreach (self::FIXTURE_ENTITY_BY_FIELD_KEYWORD as $keyword => $candidate) {
                if (\str_contains($firstField, $keyword)) {
                    $entityClass = $candidate;

                    break;
                }
            }

            $entityByAlias[$alias] = $entityClass;
            $columns = \array_values(\array_diff(\array_keys(\get_class_vars($entityClass)), ['id']));
            $columnIndex = 0;
            $columnByField = [];
            foreach (\array_keys($fields) as $field) {
                $columnByField[$field] = $field === 'id' ? 'id' : $columns[$columnIndex++ % \count($columns)];
            }

            $dql = (string) \preg_replace_callback(
                '/(?<![\w:.])'.\preg_quote($alias, '/').'\.(?<field>[A-Za-z_]\w*)/',
                static fn (array $match): string => $alias.'.'.($columnByField[$match['field']] ?? $match['field']),
                $dql
            );
        }

        return (string) \preg_replace_callback(
            '/\bEntity\s+(?<alias>[a-z]\w*)/',
            static fn (array $match): string => '\\'.($entityByAlias[$match['alias']] ?? ContainsTexts::class).' '.$match['alias'],
            $dql
        );
    }
}
