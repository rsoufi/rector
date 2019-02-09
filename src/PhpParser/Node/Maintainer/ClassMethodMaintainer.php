<?php declare(strict_types=1);

namespace Rector\PhpParser\Node\Maintainer;

use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use Rector\NodeTypeResolver\Node\Attribute;
use Rector\NodeTypeResolver\NodeTypeResolver;
use Rector\PhpParser\Node\BetterNodeFinder;
use Rector\PhpParser\Node\Resolver\NameResolver;
use Rector\PhpParser\Printer\BetterStandardPrinter;

final class ClassMethodMaintainer
{
    /**
     * @var BetterNodeFinder
     */
    private $betterNodeFinder;

    /**
     * @var BetterStandardPrinter
     */
    private $betterStandardPrinter;

    /**
     * @var NodeTypeResolver
     */
    private $nodeTypeResolver;

    /**
     * @var FunctionLikeMaintainer
     */
    private $functionLikeMaintainer;

    /**
     * @var NameResolver
     */
    private $nameResolver;

    public function __construct(
        BetterNodeFinder $betterNodeFinder,
        BetterStandardPrinter $betterStandardPrinter,
        NodeTypeResolver $nodeTypeResolver,
        FunctionLikeMaintainer $functionLikeMaintainer,
        NameResolver $nameResolver
    ) {
        $this->betterNodeFinder = $betterNodeFinder;
        $this->betterStandardPrinter = $betterStandardPrinter;
        $this->nodeTypeResolver = $nodeTypeResolver;
        $this->functionLikeMaintainer = $functionLikeMaintainer;
        $this->nameResolver = $nameResolver;
    }

    public function isParameterUsedMethod(Param $param, ClassMethod $classMethod): bool
    {
        return (bool) $this->betterNodeFinder->findFirst((array) $classMethod->stmts, function (Node $node) use (
            $param
        ) {
            return $this->betterStandardPrinter->areNodesEqual($node, $param->var);
        });
    }

    public function hasParentMethodOrInterfaceMethod(ClassMethod $classMethod): bool
    {
        $class = $classMethod->getAttribute(Attribute::CLASS_NAME);
        if (is_string($class) === false) {
            return false;
        }

        $method = $classMethod->getAttribute(Attribute::METHOD_NAME);
        if (is_string($method) === false) {
            return false;
        }

        if (! class_exists($class)) {
            return false;
        }

        if ($this->isMethodInParent($class, $method)) {
            return true;
        }

        $implementedInterfaces = class_implements($class);
        foreach ($implementedInterfaces as $implementedInterface) {
            if (method_exists($implementedInterface, $method)) {
                return true;
            }
        }

        return false;
    }

    public function hasReturnArrayOfArrays(ClassMethod $classMethodNode): bool
    {
        $statements = $classMethodNode->stmts;
        if (! $statements) {
            return false;
        }

        foreach ($statements as $statement) {
            if (! $statement instanceof Return_) {
                continue;
            }

            if (! $statement->expr instanceof Array_) {
                return false;
            }

            return $this->isArrayOfArrays($statement->expr);
        }

        return false;
    }

    /**
     * @return string[]
     */
    public function resolveReturnType(ClassMethod $classMethodNode): array
    {
        if ($classMethodNode->returnType !== null) {
            return $this->nodeTypeResolver->resolve($classMethodNode->returnType);
        }

        $staticReturnType = $this->functionLikeMaintainer->resolveStaticReturnTypeInfo($classMethodNode);
        if ($staticReturnType === null) {
            return [];
        }

        $getFqnTypeNode = $staticReturnType->getFqnTypeNode();
        if ($getFqnTypeNode === null) {
            return [];
        }

        $fqnTypeName = $this->nameResolver->resolve($getFqnTypeNode);
        if ($fqnTypeName === null) {
            return [];
        }

        return [$fqnTypeName];
    }

    /**
     * Is method actually static, or has some $this-> calls?
     */
    public function isStaticClassMethod(ClassMethod $classMethodNode): bool
    {
        return (bool) $this->betterNodeFinder->findFirst((array) $classMethodNode->stmts, function (Node $node) {
            if (! $node instanceof Variable) {
                return false;
            }

            return $this->nameResolver->isName($node, 'this');
        });
    }

    private function isMethodInParent(string $class, string $method): bool
    {
        $parentClass = $class;

        while ($parentClass = get_parent_class($parentClass)) {
            if (method_exists($parentClass, $method)) {
                return true;
            }
        }

        return false;
    }

    private function isArrayOfArrays(Node $node): bool
    {
        if (! $node instanceof Array_) {
            return false;
        }

        foreach ($node->items as $arrayItem) {
            if (! $arrayItem->value instanceof Array_) {
                return false;
            }
        }

        return true;
    }
}
