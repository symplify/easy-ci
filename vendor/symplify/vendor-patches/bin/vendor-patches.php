<?php

declare (strict_types=1);
namespace EasyCI20220525;

use EasyCI20220525\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;
use EasyCI20220525\Symplify\VendorPatches\Kernel\VendorPatchesKernel;
$possibleAutoloadPaths = [__DIR__ . '/../autoload.php', __DIR__ . '/../vendor/autoload.php', __DIR__ . '/../../../autoload.php', __DIR__ . '/../../../vendor/autoload.php'];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (!\file_exists($possibleAutoloadPath)) {
        continue;
    }
    require_once $possibleAutoloadPath;
}
$kernelBootAndApplicationRun = new \EasyCI20220525\Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun(\EasyCI20220525\Symplify\VendorPatches\Kernel\VendorPatchesKernel::class);
$kernelBootAndApplicationRun->run();