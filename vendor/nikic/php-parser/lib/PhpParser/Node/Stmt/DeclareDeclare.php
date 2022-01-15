<?php

declare (strict_types=1);
namespace EasyCI20220115\PhpParser\Node\Stmt;

use EasyCI20220115\PhpParser\Node;
class DeclareDeclare extends \EasyCI20220115\PhpParser\Node\Stmt
{
    /** @var Node\Identifier Key */
    public $key;
    /** @var Node\Expr Value */
    public $value;
    /**
     * Constructs a declare key=>value pair node.
     *
     * @param string|Node\Identifier $key        Key
     * @param Node\Expr              $value      Value
     * @param array                  $attributes Additional attributes
     */
    public function __construct($key, \EasyCI20220115\PhpParser\Node\Expr $value, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->key = \is_string($key) ? new \EasyCI20220115\PhpParser\Node\Identifier($key) : $key;
        $this->value = $value;
    }
    public function getSubNodeNames() : array
    {
        return ['key', 'value'];
    }
    public function getType() : string
    {
        return 'Stmt_DeclareDeclare';
    }
}