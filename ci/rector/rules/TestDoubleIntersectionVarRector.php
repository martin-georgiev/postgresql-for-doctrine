<?php

declare(strict_types=1);

namespace Ci\MartinGeorgiev\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Property;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IntersectionTypeNode;
use PHPStan\Reflection\ReflectionProvider;
use PHPUnit\Framework\MockObject\MockObject;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\BetterPhpDocParser\ValueObject\Type\BracketsAwareIntersectionTypeNode;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\Rector\AbstractRector;
use Rector\ValueObject\MethodName;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Names the doubled class next to the test double a property is declared as.
 *
 * Configured with a map of test double class to the factory methods producing it. A
 * property declared as one of those doubles gets a "@var Doubled&Double" docblock, with
 * the doubled class read from the "::class" the factory is handed in setUp(). The names
 * are written the way the file already spells them, so an imported class stays short.
 * A property that already carries an intersection "@var" is left as written.
 */
final class TestDoubleIntersectionVarRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, list<string>>
     */
    private array $factoryMethodNamesByDoubleClass = [];

    public function __construct(
        private readonly ReflectionProvider $reflectionProvider,
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly PhpDocTypeChanger $phpDocTypeChanger,
    ) {}

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $factoryMethodNamesByDoubleClass = [];
        foreach ($configuration as $doubleClass => $factoryMethodNames) {
            if (!\is_string($doubleClass) || $doubleClass === '' || !\is_array($factoryMethodNames) || $factoryMethodNames === []) {
                throw new \InvalidArgumentException(\sprintf(
                    '%s takes a map of test double class to the factory methods producing it',
                    self::class
                ));
            }

            if (!$this->reflectionProvider->hasClass($doubleClass)) {
                throw new \InvalidArgumentException(\sprintf(
                    'Configured test double %s cannot be found',
                    \var_export($doubleClass, true)
                ));
            }

            foreach ($factoryMethodNames as $factoryMethodName) {
                if (!\is_string($factoryMethodName) || $factoryMethodName === '') {
                    throw new \InvalidArgumentException(\sprintf(
                        'Factory methods of %s must be named as non-empty strings',
                        \var_export($doubleClass, true)
                    ));
                }

                $factoryMethodNamesByDoubleClass[$doubleClass][] = $factoryMethodName;
            }
        }

        $this->factoryMethodNamesByDoubleClass = $factoryMethodNamesByDoubleClass;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'A property declared as a test double names the doubled class in an intersection @var docblock',
            [
                new ConfiguredCodeSample(
                    <<<'CODE_SAMPLE'
                        final class BitTest extends TestCase
                        {
                            private MockObject $platform;

                            protected function setUp(): void
                            {
                                $this->platform = $this->createMock(AbstractPlatform::class);
                            }
                        }
                        CODE_SAMPLE,
                    <<<'CODE_SAMPLE'
                        final class BitTest extends TestCase
                        {
                            /**
                             * @var AbstractPlatform&MockObject
                             */
                            private MockObject $platform;

                            protected function setUp(): void
                            {
                                $this->platform = $this->createMock(AbstractPlatform::class);
                            }
                        }
                        CODE_SAMPLE,
                    [MockObject::class => ['createMock']]
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        $setUpClassMethod = $node->getMethod(MethodName::SET_UP);
        if (!$setUpClassMethod instanceof ClassMethod) {
            return null;
        }

        $hasChanged = false;
        foreach ($node->getProperties() as $property) {
            $hasChanged = $this->describeDouble($property, $setUpClassMethod) || $hasChanged;
        }

        if (!$hasChanged) {
            return null;
        }

        return $node;
    }

    private function describeDouble(Property $property, ClassMethod $setUpClassMethod): bool
    {
        if (\count($property->props) !== 1 || !$property->type instanceof Name) {
            return false;
        }

        $declaredDoubleClass = $this->getName($property->type) ?? '';
        $factoryMethodNames = $this->factoryMethodNamesByDoubleClass[$declaredDoubleClass] ?? null;
        if ($factoryMethodNames === null) {
            return false;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($property);
        $varTagValueNode = $phpDocInfo->getVarTagValueNode();
        $isAlreadyDescribed = $varTagValueNode instanceof VarTagValueNode && $varTagValueNode->type instanceof IntersectionTypeNode;
        if ($isAlreadyDescribed) {
            return false;
        }

        $propertyName = $this->getName($property->props[0]);
        if ($propertyName === null) {
            return false;
        }

        $doubledClass = $this->findDoubledClass($setUpClassMethod, $propertyName, $factoryMethodNames);
        if (!$doubledClass instanceof Name) {
            return false;
        }

        $bracketsAwareIntersectionTypeNode = new BracketsAwareIntersectionTypeNode([
            new IdentifierTypeNode($this->spellingOf($doubledClass)),
            new IdentifierTypeNode($this->spellingOf($property->type)),
        ]);
        $this->phpDocTypeChanger->changeVarTypeNode($property, $phpDocInfo, $bracketsAwareIntersectionTypeNode);

        return true;
    }

    /**
     * @param list<string> $factoryMethodNames
     *
     * @return Name|null the class the factory is handed, as the source spells it
     */
    private function findDoubledClass(ClassMethod $setUpClassMethod, string $propertyName, array $factoryMethodNames): ?Name
    {
        foreach ((array) $setUpClassMethod->stmts as $stmt) {
            if (!$stmt instanceof Expression || !$stmt->expr instanceof Assign) {
                continue;
            }

            $assign = $stmt->expr;
            if (!$assign->var instanceof PropertyFetch || !$this->isName($assign->var->name, $propertyName)) {
                continue;
            }

            $factoryCall = $this->findFactoryCall($assign->expr, $factoryMethodNames);
            if ($factoryCall === null) {
                continue;
            }

            $firstArg = $factoryCall->getArgs()[0] ?? null;
            if ($firstArg === null || !$firstArg->value instanceof ClassConstFetch || !$firstArg->value->class instanceof Name) {
                continue;
            }

            return $firstArg->value->class;
        }

        return null;
    }

    /**
     * The factory sits at the root of the call chain, so a builder reached through
     * onlyMethods()->getMock() is found by unwrapping the chain back down to it.
     *
     * @param list<string> $factoryMethodNames
     */
    private function findFactoryCall(Expr $expr, array $factoryMethodNames): MethodCall|StaticCall|null
    {
        while ($expr instanceof MethodCall || $expr instanceof StaticCall) {
            if ($this->isNames($expr->name, $factoryMethodNames)) {
                return $expr;
            }

            if (!$expr instanceof MethodCall) {
                return null;
            }

            $expr = $expr->var;
        }

        return null;
    }

    private function spellingOf(Name $name): string
    {
        $originalName = $name->getAttribute(AttributeKey::ORIGINAL_NAME);

        return $originalName instanceof Name ? $originalName->toCodeString() : $name->toCodeString();
    }
}
