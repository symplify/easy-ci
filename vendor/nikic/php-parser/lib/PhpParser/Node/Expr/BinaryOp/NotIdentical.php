<?php

declare (strict_types=1);
namespace EasyCI20220205\PhpParser\Node\Expr\BinaryOp;

use EasyCI20220205\PhpParser\Node\Expr\BinaryOp;
class NotIdentical extends \EasyCI20220205\PhpParser\Node\Expr\BinaryOp
{
    public function getOperatorSigil() : string
    {
        return '!==';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_NotIdentical';
    }
}
