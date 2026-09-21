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
 * Gives class constants matching a configured name pattern the @var type that pattern promises.
 *
 * Keys are fnmatch patterns, tried in order, so 'TYPE_NAME' matches that name alone and '*' matches every constant.
 * A constant already carrying a @var tag, or declaring a native type, is left alone - its narrower type is worth more
 * than the configured one, which is why matching everything is a deliberate choice rather than the default.
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
                    '%s takes a map of constant name pattern to the type its @var tag must carry',
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
            foreach ($this->varTypeByConstantName as $constantNamePattern => $varType) {
                if (\fnmatch($constantNamePattern, $const->name->toString())) {
                    return $varType;
                }
            }
        }

        return null;
    }
}
