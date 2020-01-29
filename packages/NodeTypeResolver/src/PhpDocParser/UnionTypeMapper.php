<?php

declare(strict_types=1);

namespace Rector\NodeTypeResolver\PhpDocParser;

use PhpParser\Node;
use PHPStan\Analyser\NameScope;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\Type\Type;
use Rector\NodeTypeResolver\Contract\PhpDocParser\PhpDocTypeMapperInterface;
use Rector\NodeTypeResolver\PhpDoc\PhpDocTypeMapper;
use Rector\NodeTypeResolver\PHPStan\Type\TypeFactory;

final class UnionTypeMapper implements PhpDocTypeMapperInterface
{
    /**
     * @var PhpDocTypeMapper
     */
    private $phpDocTypeMapper;

    /**
     * @var TypeFactory
     */
    private $typeFactory;

    public function __construct(TypeFactory $typeFactory)
    {
        $this->typeFactory = $typeFactory;
    }

    public function getNodeType(): string
    {
        return UnionTypeNode::class;
    }

    /**
     * @required
     */
    public function autowireUnionTypeMapper(PhpDocTypeMapper $phpDocTypeMapper): void
    {
        $this->phpDocTypeMapper = $phpDocTypeMapper;
    }

    /**
     * @param UnionTypeNode $typeNode
     */
    public function mapToPHPStanType(TypeNode $typeNode, Node $node, NameScope $nameScope): Type
    {
        $unionedTypes = [];
        foreach ($typeNode->types as $unionedTypeNode) {
            $unionedTypes[] = $this->phpDocTypeMapper->mapToPHPStanType($unionedTypeNode, $node, $nameScope);
        }

        // to prevent missing class error, e.g. in tests
        return $this->typeFactory->createMixedPassedOrUnionType($unionedTypes);
    }
}
