<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\Kernel;

use EasyCI20220116\Psr\Container\ContainerInterface;
use EasyCI20220116\Symplify\Astral\ValueObject\AstralConfig;
use EasyCI20220116\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use EasyCI20220116\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;
final class EasyCIKernel extends \EasyCI20220116\Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \EasyCI20220116\Psr\Container\ContainerInterface
    {
        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = \EasyCI20220116\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig::FILE_PATH;
        $configFiles[] = \EasyCI20220116\Symplify\Astral\ValueObject\AstralConfig::FILE_PATH;
        return $this->create([], [], $configFiles);
    }
}
