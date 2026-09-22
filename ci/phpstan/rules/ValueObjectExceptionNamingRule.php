<?php

declare(strict_types=1);

namespace MartinGeorgiev\PHPStan;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Keeps a value object exception named `Invalid{ValueObject}Exception` after one that exists. `ParentByNamespaceRector`
 * owns the parent of this namespace; the name is what nothing checked.
 *
 * @implements Rule<InClassNode>
 */
final readonly class ValueObjectExceptionNamingRule implements Rule
{
    /**
     * @var string
     */
    private const EXPECTED_NAME_PATTERN = '/^Invalid(?<valueObject>[A-Z][A-Za-z0-9]*)Exception\z/';

    public function __construct(private ReflectionProvider $reflectionProvider) {}

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if ($classReflection->isAnonymous() || !$classReflection->isClass()) {
            return [];
        }

        $className = $classReflection->getName();
        if (!\str_starts_with($className, ValueObjectNamespace::EXCEPTIONS)) {
            return [];
        }

        $shortName = \substr($className, \strlen(ValueObjectNamespace::EXCEPTIONS));
        if (\preg_match(self::EXPECTED_NAME_PATTERN, $shortName, $matches) !== 1) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Value object exception %s must be named Invalid{ValueObject}Exception.',
                    $className
                ))
                    ->identifier('martinGeorgiev.valueObject.exceptionName')
                    ->build(),
            ];
        }

        $namedValueObject = ValueObjectNamespace::VALUE_OBJECTS.$matches['valueObject'];
        $namesAValueObject = $this->reflectionProvider->hasClass($namedValueObject)
            && ValueObjectNamespace::isValueObject($this->reflectionProvider->getClass($namedValueObject));
        if (!$namesAValueObject) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Value object exception %s names %s, which is not a value object.',
                    $className,
                    $namedValueObject
                ))
                    ->identifier('martinGeorgiev.valueObject.exceptionNamesAnUnknownValueObject')
                    ->build(),
            ];
        }

        return [];
    }
}
