<?php

declare(strict_types=1);

namespace MartinGeorgiev\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\BetterPhpDocParser\PhpDocManipulator\PhpDocTypeChanger;
use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Keeps a class constant declared under a configured name carrying the @var type that name promises.
 *
 * Configured with a map of constant name to the type written verbatim into the tag. A matching
 * constant with no @var tag gets one; a constant that already carries one is left untouched, so a
 * deliberately narrower type survives. An existing docblock is appended to, never replaced, and a
 * constant that declares a native type already states it and is skipped.
 */
final class VarTagByConstantNameRector extends AbstractRector implements ConfigurableRectorInterface
{
    /**
     * @var array<string, string>
     */
    private array $varTypeByConstantName = [];

    public function __construct(
        private readonly PhpDocInfoFactory $phpDocInfoFactory,
        private readonly PhpDocTypeChanger $phpDocTypeChanger
    ) {}

    /**
     * @param mixed[] $configuration
     */
    public function configure(array $configuration): void
    {
        $varTypeByConstantName = [];
        foreach ($configuration as $constantName => $varType) {
            if (!\is_string($constantName) || $constantName === '' || !\is_string($varType) || $varType === '') {
                throw new \InvalidArgumentException(\sprintf(
                    '%s takes a map of constant name to the type its @var tag must carry',
                    self::class
                ));
            }

            $varTypeByConstantName[$constantName] = $varType;
        }

        $this->varTypeByConstantName = $varTypeByConstantName;
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Class constants named by the configuration carry a @var tag with the configured type',
            [
                new ConfiguredCodeSample(
                    'protected const TYPE_NAME = Type::CITEXT;',
                    "/**\n * @var string\n */\nprotected const TYPE_NAME = Type::CITEXT;",
                    ['TYPE_NAME' => 'string']
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
        if (!$node instanceof ClassConst) {
            return null;
        }

        $aNativeTypeAlreadyStatesTheType = $node->type instanceof Node;
        if ($aNativeTypeAlreadyStatesTheType) {
            return null;
        }

        $varType = $this->matchVarType($node);
        if ($varType === null) {
            return null;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($node);
        if ($phpDocInfo->getVarTagValueNode() instanceof VarTagValueNode) {
            return null;
        }

        $this->phpDocTypeChanger->changeVarTypeNode($node, $phpDocInfo, new IdentifierTypeNode($varType));

        return $node;
    }

    private function matchVarType(ClassConst $classConst): ?string
    {
        foreach ($classConst->consts as $const) {
            $varType = $this->varTypeByConstantName[$const->name->toString()] ?? null;
            if ($varType !== null) {
                return $varType;
            }
        }

        return null;
    }
}
