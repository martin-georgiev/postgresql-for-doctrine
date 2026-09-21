<?php

declare(strict_types=1);

namespace MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Keeps the class-level docblock a namespace promises present on its classes.
 *
 * Keys are namespace prefixes, longest match winning, so `Types\Exceptions\` owes less than `Types\`.
 * Only concrete classes are checked: an abstract base has no PostgreSQL page to cite and no function name to name.
 * The docblock is read off the class node, so a parent's `@since` cannot satisfy a child declaring none.
 *
 * @implements Rule<InClassNode>
 */
final readonly class ClassDocBlockRule implements Rule
{
    /**
     * @var string
     */
    public const REQUIREMENT_DESCRIPTION = 'description';

    /**
     * @var string
     */
    public const REQUIREMENT_FIRST_LINE_NAMES_IMPLEMENTATION = 'implementationFirstLine';

    /**
     * A tag must carry a value; a bare `@since` fails.
     *
     * @var array<int, string>
     */
    private const SUPPORTED_TAGS = ['see', 'since', 'author'];

    /**
     * The vendors whose names a DQL function docblock may open with.
     *
     * @var string
     */
    private const FIRST_LINE_PATTERN = '/^Implementation of (?:PostgreSQL|PostGIS) \S.*\.\z/';

    /**
     * PostGIS spells its functions `ST_Area`, not `ST_AREA`.
     *
     * @var string
     */
    private const CALLABLE_TOKEN_PATTERN = '/\b([A-Za-z_][A-Za-z0-9_]*)\(\)/';

    /**
     * @var array<string, array<int, string>>
     */
    private array $requirementsByNamespacePrefix;

    /**
     * @param array<string, array<int, string>> $requirementsByNamespacePrefix prefix to the requirements owed under it
     * @param array<int, string> $grandfatheredAuthorNames values accepted despite not being real names
     */
    public function __construct(
        array $requirementsByNamespacePrefix,
        private array $grandfatheredAuthorNames = [],
    ) {
        foreach ($requirementsByNamespacePrefix as $namespacePrefix => $requirements) {
            foreach ($requirements as $requirement) {
                $isKnownRequirement = $requirement === self::REQUIREMENT_DESCRIPTION
                    || $requirement === self::REQUIREMENT_FIRST_LINE_NAMES_IMPLEMENTATION
                    || \in_array($requirement, self::SUPPORTED_TAGS, true);
                if (!$isKnownRequirement) {
                    throw new \InvalidArgumentException(\sprintf(
                        '%s cannot require %s under %s',
                        self::class,
                        \var_export($requirement, true),
                        \var_export($namespacePrefix, true)
                    ));
                }
            }
        }

        // Longest prefix first, so a sub-namespace's entry wins over its parent's regardless of config order.
        \uksort(
            $requirementsByNamespacePrefix,
            static fn (string $left, string $right): int => \strlen($right) <=> \strlen($left)
        );

        $this->requirementsByNamespacePrefix = $requirementsByNamespacePrefix;
    }

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    /**
     * @return array<int, IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $classLike = $node->getOriginalNode();
        if (!$classLike instanceof Class_ || $classLike->isAnonymous() || $classLike->isAbstract()) {
            return [];
        }

        $className = $node->getClassReflection()->getName();
        $requirements = $this->matchRequirements($className);
        if ($requirements === []) {
            return [];
        }

        $docBlock = $classLike->getDocComment()?->getText() ?? '';
        $description = $this->extractDescription($docBlock);

        $errors = [];

        foreach (self::SUPPORTED_TAGS as $tag) {
            if (\in_array($tag, $requirements, true) && !$this->hasTagWithAValue($docBlock, $tag)) {
                $errors[] = RuleErrorBuilder::message(\sprintf(
                    'Class %s has no @%s in its class-level docblock.',
                    $className,
                    $tag
                ))->identifier('martinGeorgiev.classDocblock.missingTag')->build();
            }
        }

        if (\in_array(self::REQUIREMENT_DESCRIPTION, $requirements, true) && $description === []) {
            $errors[] = RuleErrorBuilder::message(\sprintf(
                'Class %s has no description in its class-level docblock.',
                $className
            ))->identifier('martinGeorgiev.classDocblock.missingDescription')->build();
        }

        if (\in_array(self::REQUIREMENT_FIRST_LINE_NAMES_IMPLEMENTATION, $requirements, true)) {
            $errors = \array_merge($errors, $this->checkFirstLine($className, $description));
        }

        return \array_merge($errors, $this->checkAuthorFormat($className, $docBlock));
    }

    /**
     * @return array<int, string>
     */
    private function matchRequirements(string $className): array
    {
        foreach ($this->requirementsByNamespacePrefix as $namespacePrefix => $requirements) {
            if (\str_starts_with($className, $namespacePrefix)) {
                return $requirements;
            }
        }

        return [];
    }

    /**
     * The lines of running text before the first tag — what the docblock says in its own words.
     *
     * @return array<int, string>
     */
    private function extractDescription(string $docBlock): array
    {
        if ($docBlock === '') {
            return [];
        }

        $description = [];
        foreach (\preg_split('/\R/', $docBlock) ?: [] as $line) {
            $line = \trim($line);
            $line = (string) \preg_replace('#^/\*\*#', '', $line);
            $line = (string) \preg_replace('#\*/\z#', '', $line);
            $line = \trim((string) \preg_replace('#^\*\s?#', '', \trim($line)));
            if ($line === '') {
                continue;
            }

            if (\str_starts_with($line, '@')) {
                break;
            }

            $description[] = $line;
        }

        return $description;
    }

    private function hasTagWithAValue(string $docBlock, string $tag): bool
    {
        return \preg_match('/^\h*(?:\/\*\*|\*)\h*@'.\preg_quote($tag, '/').'\h+(?!\*\/)\S/m', $docBlock) === 1;
    }

    /**
     * @param array<int, string> $description
     *
     * @return array<int, IdentifierRuleError>
     */
    private function checkFirstLine(string $className, array $description): array
    {
        $firstLine = $description[0] ?? '';
        if ($firstLine === '') {
            return [];
        }

        if (\preg_match(self::FIRST_LINE_PATTERN, $firstLine) !== 1) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Class %s opens its docblock with %s, not "Implementation of PostgreSQL ....".',
                    $className,
                    \var_export($firstLine, true)
                ))->identifier('martinGeorgiev.classDocblock.firstLine')->build(),
            ];
        }

        $errors = [];
        \preg_match_all(self::CALLABLE_TOKEN_PATTERN, $firstLine, $matches);
        foreach ($matches[1] as $callableToken) {
            if (\str_starts_with($callableToken, 'ST_')) {
                continue;
            }

            if ($callableToken !== \strtoupper($callableToken)) {
                $errors[] = RuleErrorBuilder::message(\sprintf(
                    'Class %s names %s() in its docblock first line, which is not uppercase.',
                    $className,
                    $callableToken
                ))->identifier('martinGeorgiev.classDocblock.firstLine')->build();
            }
        }

        return $errors;
    }

    /**
     * @return array<int, IdentifierRuleError>
     */
    private function checkAuthorFormat(string $className, string $docBlock): array
    {
        \preg_match_all('/^\s*\*\s*@author\s+(.+)$/m', $docBlock, $matches);

        $errors = [];
        foreach ($matches[1] as $author) {
            $author = \trim($author);
            if (\in_array($author, $this->grandfatheredAuthorNames, true)) {
                continue;
            }

            $name = \trim((string) \preg_replace('/<.*\z/', '', $author));
            $isARealName = \preg_match('/^\p{Lu}/u', $name) === 1;
            $pointsAtAHandleUrl = \preg_match('#<\s*https?://#i', $author) === 1;
            if ($isARealName && !$pointsAtAHandleUrl) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(\sprintf(
                'Class %s has @author %s, which is not a real name optionally followed by an email.',
                $className,
                \var_export($author, true)
            ))->identifier('martinGeorgiev.classDocblock.authorFormat')->build();
        }

        return $errors;
    }
}
