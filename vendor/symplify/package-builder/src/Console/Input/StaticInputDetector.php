<?php

declare (strict_types=1);
namespace EasyCI20220115\Symplify\PackageBuilder\Console\Input;

use EasyCI20220115\Symfony\Component\Console\Input\ArgvInput;
/**
 * @api
 */
final class StaticInputDetector
{
    public static function isDebug() : bool
    {
        $argvInput = new \EasyCI20220115\Symfony\Component\Console\Input\ArgvInput();
        return $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
    }
}