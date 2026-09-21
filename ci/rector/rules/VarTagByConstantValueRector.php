<?php

declare(strict_types=1);

namespace MartinGeorgiev\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\Type\Type;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Gives every class constant a @var tag carrying the type of the value assigned to it.
 */
final class VarTagByConstantValueRector extends AbstractRector
{
    public function __construct(
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly PhpDocTypeChanger $phpDocTypeChanger
    ) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Class constants carry a @var tag naming the type of their value',
            [
                new CodeSample(
                    'private const MAX_DIMENSIONS = 100;',
                    "/**\n * @var int\n */\nprivate const MAX_DIMENSIONS = 100;"
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [ClassConst::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof ClassConst || $node->type instanceof Node) {
            return null;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        if ($phpDocInfo->getVarTagValueNode() instanceof VarTagValueNode) {
            return null;
        }

        $varType = $this->describeValueType($node);
        if ($varType === null) {
            return null;
        }

        $this->phpDocTypeChanger->changeVarTypeNode($node, $phpDocInfo, new IdentifierTypeNode($varType));

        return $node;
    }

    /**
     * A constant holding anything else - a union across a grouped declaration, an enum case, a type the scope cannot
     * resolve - is left untagged rather than described loosely.
     */
    private function describeValueType(ClassConst $classConst): ?string
    {
        $varTypes = [];
        foreach ($classConst->consts as $const) {
            $varTypes[] = $this->nameSupportedType($this->getType($const->value));
        }

        $varTypes = \array_unique($varTypes);

        return \count($varTypes) === 1 ? \reset($varTypes) : null;
    }

    /**
     * Named by asking the type what it is.
     */
    private function nameSupportedType(Type $type): ?string
    {
        return match (true) {
            $type->isString()->yes() => 'string',
            $type->isInteger()->yes() => 'int',
            $type->isFloat()->yes() => 'float',
            $type->isBoolean()->yes() => 'bool',
            $type->isArray()->yes() => 'array',
            $type->isNull()->yes() => 'null',
            $type->isObject()->yes() => $this->nameObjectClass($type),
            default => null,
        };
    }

    /**
     * An enum case is the only object a PHP 8.2 constant expression can produce.
     * The tag names its enum rather than the case, which the value already shows.
     */
    private function nameObjectClass(Type $type): ?string
    {
        $objectClassNames = $type->getObjectClassNames();

        return \count($objectClassNames) === 1 ? '\\'.\reset($objectClassNames) : null;
    }
}
