<?php

declare (strict_types=1);
namespace EasyCI202211\PhpParser\Node\Expr\AssignOp;

use EasyCI202211\PhpParser\Node\Expr\AssignOp;
class ShiftRight extends AssignOp
{
    public function getType() : string
    {
        return 'Expr_AssignOp_ShiftRight';
    }
}
