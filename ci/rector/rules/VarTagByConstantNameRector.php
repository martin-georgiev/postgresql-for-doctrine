<?php

declare(strict_types=1);

namespace MartinGeorgiev\Rector;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst;
use PHPStan\PhpDocParser\Ast\PhpDoc\VarTagValueNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
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
 * A constant is skipped when it already carries a @var tag, declares a native type, or holds a value the configured
 * type would misdescribe - a broad pattern must not annotate an int constant as a string.
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
                if (\fnmatch($constantNamePattern, $const->name->toString()) && $this->describesValue($varType, $const->value)) {
                    return $varType;
                }
            }
        }

        return null;
    }

    /**
     * A configured type this rule cannot model is taken on trust; a scalar one must accept the value.
     */
    private function describesValue(string $varType, Node\Expr $expr): bool
    {
        $configuredType = match ($varType) {
            'string' => new StringType(),
            'int' => new IntegerType(),
            'float' => new FloatType(),
            'bool' => new BooleanType(),
            'array' => new ArrayType(new MixedType(), new MixedType()),
            default => null,
        };

        return !$configuredType instanceof Type || $configuredType->isSuperTypeOf($this->getType($expr))->yes();
    }
}
