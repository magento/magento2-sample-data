<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\DownloadableSampleData\Setup\Patch\Data;

use Magento\Framework\Setup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class InstallDownloadableSampleData
 * @package Magento\DownloadableSampleData\Setup\Patch\Data
 */
class InstallDownloadableSampleData implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var Setup\SampleData\Executor
     */
    protected $executor;

    /**
     * @var \Magento\DownloadableSampleData\Setup\Installer
     */
    protected $installer;

    /**
     * InstallDownloadableSampleData constructor.
     * @param Setup\SampleData\Executor $executor
     * @param \Magento\DownloadableSampleData\Setup\Installer $installer
     */
    public function __construct(
        Setup\SampleData\Executor $executor,
        \Magento\DownloadableSampleData\Setup\Installer $installer
    ) {
        $this->executor = $executor;
        $this->installer = $installer;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->executor->exec($this->installer);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
