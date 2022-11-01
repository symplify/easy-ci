<?php

declare (strict_types=1);
namespace EasyCI202211\PhpParser\Node\Expr\BinaryOp;

use EasyCI202211\PhpParser\Node\Expr\BinaryOp;
class LogicalOr extends BinaryOp
{
    public function getOperatorSigil() : string
    {
        return 'or';
    }
    public function getType() : string
    {
        return 'Expr_BinaryOp_LogicalOr';
    }
}
