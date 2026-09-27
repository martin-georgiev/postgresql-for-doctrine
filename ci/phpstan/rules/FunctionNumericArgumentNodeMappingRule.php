<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\PHPStan;

use MartinGeorgiev\Doctrine\ORM\Query\AST\Functions\BaseFunction;
use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * A numeric argument has to be read by SimpleArithmeticExpression. ArithmeticPrimary takes no sign, so DQL rejects a
 * call such as `ST_MAKEPOINT(-71.1, 42.3)` before PostgreSQL sees it.
 *
 * A function names its parser methods in three places: the node mapping pattern of a variadic function, the
 * addNodeMapping() calls of a fixed one, and a direct call on the parser.
 *
 * @implements Rule<InClassNode>
 */
final class FunctionNumericArgumentNodeMappingRule implements Rule
{
    /**
     * @var string
     */
    private const UNSIGNED_NODE_TYPE = 'ArithmeticPrimary';

    /**
     * @var string
     */
    private const SIGNED_NODE_TYPE = 'SimpleArithmeticExpression';

    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        if (!$classReflection->is(BaseFunction::class)) {
            return [];
        }

        $classLike = $node->getOriginalNode();
        $className = $classReflection->getDisplayName();
        $errors = [];

        $declaration = VariadicFunctionDeclaration::fromClass($classLike, $scope);
        if ($declaration instanceof VariadicFunctionDeclaration) {
            foreach ($declaration->getPatterns() as $pattern) {
                if (\in_array(self::UNSIGNED_NODE_TYPE, VariadicFunctionDeclaration::slotsOf($pattern), true)) {
                    $errors[] = $this->buildError($className, \sprintf('its node mapping pattern "%s"', $pattern), $declaration->getDeclarationLine());
                }
            }
        }

        foreach ((new NodeFinder())->findInstanceOf($classLike, MethodCall::class) as $methodCall) {
            if (!$methodCall->name instanceof Identifier) {
                continue;
            }

            $methodName = $methodCall->name->toString();
            if ($methodName === self::UNSIGNED_NODE_TYPE) {
                $errors[] = $this->buildError($className, 'a direct parser call', $methodCall->getStartLine());

                continue;
            }

            $mappedNodeType = $methodCall->getArgs()[0]->value ?? null;
            if ($methodName === 'addNodeMapping' && $mappedNodeType instanceof String_ && $mappedNodeType->value === self::UNSIGNED_NODE_TYPE) {
                $errors[] = $this->buildError($className, 'addNodeMapping()', $methodCall->getStartLine());
            }
        }

        return $errors;
    }

    private function buildError(string $className, string $source, int $line): IdentifierRuleError
    {
        return RuleErrorBuilder::message(\sprintf(
            '%s reads an argument with %s in %s, which rejects a signed number such as -1. Read it with %s.',
            $className,
            self::UNSIGNED_NODE_TYPE,
            $source,
            self::SIGNED_NODE_TYPE
        ))
            ->identifier('martinGeorgiev.function.numericArgumentNodeMapping')
            ->line($line)
            ->build();
    }
}
