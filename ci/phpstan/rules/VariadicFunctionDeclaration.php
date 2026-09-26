<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;

/**
 * What a variadic DQL function declares about its arguments: `getNodeMappingPattern()` returns one comma-separated
 * entry per accepted shape. Read off the class body once here rather than by each rule, and skipped when a method
 * returns anything other than a literal.
 */
final readonly class VariadicFunctionDeclaration
{
    /**
     * @param list<string> $patterns
     */
    private function __construct(
        private array $patterns,
        private int $declarationLine,
        private ?int $maxArgumentCount
    ) {}

    public static function fromClass(ClassLike $classLike, Scope $scope): ?self
    {
        $patternMethod = $classLike->getMethod('getNodeMappingPattern');
        if (!$patternMethod instanceof ClassMethod) {
            return null;
        }

        $patterns = self::readPatterns($patternMethod, $scope);
        if ($patterns === null) {
            return null;
        }

        return new self(
            $patterns,
            $patternMethod->getStartLine(),
            self::readIntegerReturnedBy($classLike, 'getMaxArgumentCount', $scope)
        );
    }

    /**
     * The parser methods a pattern names, in argument order.
     *
     * @return list<string>
     */
    public static function slotsOf(string $pattern): array
    {
        return \array_map(\trim(...), \explode(',', $pattern));
    }

    /**
     * @return list<string>
     */
    public function getPatterns(): array
    {
        return $this->patterns;
    }

    public function getDeclarationLine(): int
    {
        return $this->declarationLine;
    }

    public function getMaxArgumentCount(): ?int
    {
        return $this->maxArgumentCount;
    }

    /**
     * @return list<string>|null
     */
    private static function readPatterns(ClassMethod $classMethod, Scope $scope): ?array
    {
        $returned = self::findReturnedExpression($classMethod);
        if (!$returned instanceof Expr) {
            return null;
        }

        $constantArrays = $scope->getType($returned)->getConstantArrays();
        if (\count($constantArrays) !== 1) {
            return null;
        }

        $patterns = [];
        foreach ($constantArrays[0]->getValueTypes() as $valueType) {
            $constantStrings = $valueType->getConstantStrings();
            if (\count($constantStrings) !== 1) {
                return null;
            }

            $patterns[] = $constantStrings[0]->getValue();
        }

        return $patterns === [] ? null : $patterns;
    }

    private static function readIntegerReturnedBy(ClassLike $classLike, string $methodName, Scope $scope): ?int
    {
        $method = $classLike->getMethod($methodName);
        if (!$method instanceof ClassMethod) {
            return null;
        }

        $returned = self::findReturnedExpression($method);
        if (!$returned instanceof Expr) {
            return null;
        }

        $values = $scope->getType($returned)->getConstantScalarValues();
        if (\count($values) !== 1 || !\is_int($values[0])) {
            return null;
        }

        return $values[0];
    }

    private static function findReturnedExpression(ClassMethod $classMethod): ?Expr
    {
        foreach ($classMethod->stmts ?? [] as $statement) {
            if ($statement instanceof Return_) {
                return $statement->expr;
            }
        }

        return null;
    }
}
