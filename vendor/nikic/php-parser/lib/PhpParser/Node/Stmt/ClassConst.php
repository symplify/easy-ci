<?php

declare (strict_types=1);
namespace EasyCI20220115\PhpParser\Node\Stmt;

use EasyCI20220115\PhpParser\Node;
class ClassConst extends \EasyCI20220115\PhpParser\Node\Stmt
{
    /** @var int Modifiers */
    public $flags;
    /** @var Node\Const_[] Constant declarations */
    public $consts;
    /** @var Node\AttributeGroup[] */
    public $attrGroups;
    /**
     * Constructs a class const list node.
     *
     * @param Node\Const_[]         $consts     Constant declarations
     * @param int                   $flags      Modifiers
     * @param array                 $attributes Additional attributes
     * @param Node\AttributeGroup[] $attrGroups PHP attribute groups
     */
    public function __construct(array $consts, int $flags = 0, array $attributes = [], array $attrGroups = [])
    {
        $this->attributes = $attributes;
        $this->flags = $flags;
        $this->consts = $consts;
        $this->attrGroups = $attrGroups;
    }
    public function getSubNodeNames() : array
    {
        return ['attrGroups', 'flags', 'consts'];
    }
    /**
     * Whether constant is explicitly or implicitly public.
     *
     * @return bool
     */
    public function isPublic() : bool
    {
        return ($this->flags & \EasyCI20220115\PhpParser\Node\Stmt\Class_::MODIFIER_PUBLIC) !== 0 || ($this->flags & \EasyCI20220115\PhpParser\Node\Stmt\Class_::VISIBILITY_MODIFIER_MASK) === 0;
    }
    /**
     * Whether constant is protected.
     *
     * @return bool
     */
    public function isProtected() : bool
    {
        return (bool) ($this->flags & \EasyCI20220115\PhpParser\Node\Stmt\Class_::MODIFIER_PROTECTED);
    }
    /**
     * Whether constant is private.
     *
     * @return bool
     */
    public function isPrivate() : bool
    {
        return (bool) ($this->flags & \EasyCI20220115\PhpParser\Node\Stmt\Class_::MODIFIER_PRIVATE);
    }
    /**
     * Whether constant is final.
     *
     * @return bool
     */
    public function isFinal() : bool
    {
        return (bool) ($this->flags & \EasyCI20220115\PhpParser\Node\Stmt\Class_::MODIFIER_FINAL);
    }
    public function getType() : string
    {
        return 'Stmt_ClassConst';
    }
}