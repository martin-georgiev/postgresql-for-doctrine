<?php

declare(strict_types=1);

namespace MartinGeorgiev\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\Type\VerbosityLevel;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Gives every class constant a @var tag carrying the type of the value assigned to it.
 *
 * The type is read from the value, never configured, so the tag cannot contradict what the constant holds. A constant
 * already carrying a @var is left alone - a narrower hand-written type is worth keeping, and PHPStan reports one that
 * disagrees with the value as classConstant.phpDocType. A constant declaring a native type already states it.
 */
final class ConstantVarTagRector extends AbstractRector
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
     * A value whose type cannot be named in a tag - a union, or something the scope cannot resolve - is left untagged
     * rather than described as mixed, which would say less than the inferred type already does.
     */
    private function describeValueType(ClassConst $classConst): ?string
    {
        $describedTypes = [];
        foreach ($classConst->consts as $const) {
            $describedTypes[] = $this->getType($const->value)->describe(VerbosityLevel::typeOnly());
        }

        $describedTypes = \array_unique($describedTypes);
        if (\count($describedTypes) !== 1) {
            return null;
        }

        $varType = \reset($describedTypes);

        return \preg_match('/^[A-Za-z_\\\\][A-Za-z0-9_\\\\]*\z/', $varType) === 1 && $varType !== 'mixed' ? $varType : null;
    }
}
