<?php

declare (strict_types=1);
namespace EasyCI20220115\PhpParser\NodeVisitor;

use EasyCI20220115\PhpParser\ErrorHandler;
use EasyCI20220115\PhpParser\NameContext;
use EasyCI20220115\PhpParser\Node;
use EasyCI20220115\PhpParser\Node\Expr;
use EasyCI20220115\PhpParser\Node\Name;
use EasyCI20220115\PhpParser\Node\Name\FullyQualified;
use EasyCI20220115\PhpParser\Node\Stmt;
use EasyCI20220115\PhpParser\NodeVisitorAbstract;
class NameResolver extends \EasyCI20220115\PhpParser\NodeVisitorAbstract
{
    /** @var NameContext Naming context */
    protected $nameContext;
    /** @var bool Whether to preserve original names */
    protected $preserveOriginalNames;
    /** @var bool Whether to replace resolved nodes in place, or to add resolvedNode attributes */
    protected $replaceNodes;
    /**
     * Constructs a name resolution visitor.
     *
     * Options:
     *  * preserveOriginalNames (default false): An "originalName" attribute will be added to
     *    all name nodes that underwent resolution.
     *  * replaceNodes (default true): Resolved names are replaced in-place. Otherwise, a
     *    resolvedName attribute is added. (Names that cannot be statically resolved receive a
     *    namespacedName attribute, as usual.)
     *
     * @param ErrorHandler|null $errorHandler Error handler
     * @param array $options Options
     */
    public function __construct(\EasyCI20220115\PhpParser\ErrorHandler $errorHandler = null, array $options = [])
    {
        $this->nameContext = new \EasyCI20220115\PhpParser\NameContext($errorHandler ?? new \EasyCI20220115\PhpParser\ErrorHandler\Throwing());
        $this->preserveOriginalNames = $options['preserveOriginalNames'] ?? \false;
        $this->replaceNodes = $options['replaceNodes'] ?? \true;
    }
    /**
     * Get name resolution context.
     *
     * @return NameContext
     */
    public function getNameContext() : \EasyCI20220115\PhpParser\NameContext
    {
        return $this->nameContext;
    }
    public function beforeTraverse(array $nodes)
    {
        $this->nameContext->startNamespace();
        return null;
    }
    public function enterNode(\EasyCI20220115\PhpParser\Node $node)
    {
        if ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Namespace_) {
            $this->nameContext->startNamespace($node->name);
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Use_) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, null);
            }
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\GroupUse) {
            foreach ($node->uses as $use) {
                $this->addAlias($use, $node->type, $node->prefix);
            }
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Class_) {
            if (null !== $node->extends) {
                $node->extends = $this->resolveClassName($node->extends);
            }
            foreach ($node->implements as &$interface) {
                $interface = $this->resolveClassName($interface);
            }
            $this->resolveAttrGroups($node);
            if (null !== $node->name) {
                $this->addNamespacedName($node);
            }
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Interface_) {
            foreach ($node->extends as &$interface) {
                $interface = $this->resolveClassName($interface);
            }
            $this->resolveAttrGroups($node);
            $this->addNamespacedName($node);
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Enum_) {
            foreach ($node->implements as &$interface) {
                $interface = $this->resolveClassName($interface);
            }
            $this->resolveAttrGroups($node);
            if (null !== $node->name) {
                $this->addNamespacedName($node);
            }
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Trait_) {
            $this->resolveAttrGroups($node);
            $this->addNamespacedName($node);
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Function_) {
            $this->resolveSignature($node);
            $this->resolveAttrGroups($node);
            $this->addNamespacedName($node);
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\ClassMethod || $node instanceof \EasyCI20220115\PhpParser\Node\Expr\Closure || $node instanceof \EasyCI20220115\PhpParser\Node\Expr\ArrowFunction) {
            $this->resolveSignature($node);
            $this->resolveAttrGroups($node);
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Property) {
            if (null !== $node->type) {
                $node->type = $this->resolveType($node->type);
            }
            $this->resolveAttrGroups($node);
        } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Const_) {
            foreach ($node->consts as $const) {
                $this->addNamespacedName($const);
            }
        } else {
            if ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\ClassConst) {
                $this->resolveAttrGroups($node);
            } else {
                if ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\EnumCase) {
                    $this->resolveAttrGroups($node);
                } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Expr\StaticCall || $node instanceof \EasyCI20220115\PhpParser\Node\Expr\StaticPropertyFetch || $node instanceof \EasyCI20220115\PhpParser\Node\Expr\ClassConstFetch || $node instanceof \EasyCI20220115\PhpParser\Node\Expr\New_ || $node instanceof \EasyCI20220115\PhpParser\Node\Expr\Instanceof_) {
                    if ($node->class instanceof \EasyCI20220115\PhpParser\Node\Name) {
                        $node->class = $this->resolveClassName($node->class);
                    }
                } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\Catch_) {
                    foreach ($node->types as &$type) {
                        $type = $this->resolveClassName($type);
                    }
                } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Expr\FuncCall) {
                    if ($node->name instanceof \EasyCI20220115\PhpParser\Node\Name) {
                        $node->name = $this->resolveName($node->name, \EasyCI20220115\PhpParser\Node\Stmt\Use_::TYPE_FUNCTION);
                    }
                } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Expr\ConstFetch) {
                    $node->name = $this->resolveName($node->name, \EasyCI20220115\PhpParser\Node\Stmt\Use_::TYPE_CONSTANT);
                } elseif ($node instanceof \EasyCI20220115\PhpParser\Node\Stmt\TraitUse) {
                    foreach ($node->traits as &$trait) {
                        $trait = $this->resolveClassName($trait);
                    }
                    foreach ($node->adaptations as $adaptation) {
                        if (null !== $adaptation->trait) {
                            $adaptation->trait = $this->resolveClassName($adaptation->trait);
                        }
                        if ($adaptation instanceof \EasyCI20220115\PhpParser\Node\Stmt\TraitUseAdaptation\Precedence) {
                            foreach ($adaptation->insteadof as &$insteadof) {
                                $insteadof = $this->resolveClassName($insteadof);
                            }
                        }
                    }
                }
            }
        }
        return null;
    }
    private function addAlias(\EasyCI20220115\PhpParser\Node\Stmt\UseUse $use, $type, \EasyCI20220115\PhpParser\Node\Name $prefix = null)
    {
        // Add prefix for group uses
        $name = $prefix ? \EasyCI20220115\PhpParser\Node\Name::concat($prefix, $use->name) : $use->name;
        // Type is determined either by individual element or whole use declaration
        $type |= $use->type;
        $this->nameContext->addAlias($name, (string) $use->getAlias(), $type, $use->getAttributes());
    }
    /** @param Stmt\Function_|Stmt\ClassMethod|Expr\Closure $node */
    private function resolveSignature($node)
    {
        foreach ($node->params as $param) {
            $param->type = $this->resolveType($param->type);
            $this->resolveAttrGroups($param);
        }
        $node->returnType = $this->resolveType($node->returnType);
    }
    private function resolveType($node)
    {
        if ($node instanceof \EasyCI20220115\PhpParser\Node\Name) {
            return $this->resolveClassName($node);
        }
        if ($node instanceof \EasyCI20220115\PhpParser\Node\NullableType) {
            $node->type = $this->resolveType($node->type);
            return $node;
        }
        if ($node instanceof \EasyCI20220115\PhpParser\Node\UnionType || $node instanceof \EasyCI20220115\PhpParser\Node\IntersectionType) {
            foreach ($node->types as &$type) {
                $type = $this->resolveType($type);
            }
            return $node;
        }
        return $node;
    }
    /**
     * Resolve name, according to name resolver options.
     *
     * @param Name $name Function or constant name to resolve
     * @param int  $type One of Stmt\Use_::TYPE_*
     *
     * @return Name Resolved name, or original name with attribute
     */
    protected function resolveName(\EasyCI20220115\PhpParser\Node\Name $name, int $type) : \EasyCI20220115\PhpParser\Node\Name
    {
        if (!$this->replaceNodes) {
            $resolvedName = $this->nameContext->getResolvedName($name, $type);
            if (null !== $resolvedName) {
                $name->setAttribute('resolvedName', $resolvedName);
            } else {
                $name->setAttribute('namespacedName', \EasyCI20220115\PhpParser\Node\Name\FullyQualified::concat($this->nameContext->getNamespace(), $name, $name->getAttributes()));
            }
            return $name;
        }
        if ($this->preserveOriginalNames) {
            // Save the original name
            $originalName = $name;
            $name = clone $originalName;
            $name->setAttribute('originalName', $originalName);
        }
        $resolvedName = $this->nameContext->getResolvedName($name, $type);
        if (null !== $resolvedName) {
            return $resolvedName;
        }
        // unqualified names inside a namespace cannot be resolved at compile-time
        // add the namespaced version of the name as an attribute
        $name->setAttribute('namespacedName', \EasyCI20220115\PhpParser\Node\Name\FullyQualified::concat($this->nameContext->getNamespace(), $name, $name->getAttributes()));
        return $name;
    }
    protected function resolveClassName(\EasyCI20220115\PhpParser\Node\Name $name)
    {
        return $this->resolveName($name, \EasyCI20220115\PhpParser\Node\Stmt\Use_::TYPE_NORMAL);
    }
    protected function addNamespacedName(\EasyCI20220115\PhpParser\Node $node)
    {
        $node->namespacedName = \EasyCI20220115\PhpParser\Node\Name::concat($this->nameContext->getNamespace(), (string) $node->name);
    }
    protected function resolveAttrGroups(\EasyCI20220115\PhpParser\Node $node)
    {
        foreach ($node->attrGroups as $attrGroup) {
            foreach ($attrGroup->attrs as $attr) {
                $attr->name = $this->resolveClassName($attr->name);
            }
        }
    }
}