<?php

declare (strict_types=1);
namespace EasyCI20220116\Symplify\EasyCI\StaticDetector\CurrentProvider;

use EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo;
final class CurrentFileInfoProvider
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $smartFileInfo;
    public function setCurrentFileInfo(\EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo) : void
    {
        $this->smartFileInfo = $smartFileInfo;
    }
    public function getSmartFileInfo() : \EasyCI20220116\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->smartFileInfo;
    }
}
