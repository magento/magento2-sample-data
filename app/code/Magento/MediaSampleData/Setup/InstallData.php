<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MediaSampleData\Setup;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Setup;

class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var string
     */
    private $path;

    public function __construct(
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->path = $filesystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath();
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $destination = $this->path;
        foreach (
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(__DIR__ . '/../media', \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                if (!file_exists($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName())) {
                    mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
                }
            } else {
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}
