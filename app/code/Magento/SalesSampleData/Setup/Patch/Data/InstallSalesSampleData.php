<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SalesSampleData\Setup\Patch\Data;

use Magento\Framework\Indexer\AbstractProcessor;
use Magento\Framework\Setup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class InstallSalesSampleData
 * @package Magento\SalesSampleData\Setup\Patch\Data
 */
class InstallSalesSampleData implements
    DataPatchInterface,
    PatchVersionInterface,
    NonTransactionableInterface
{
    /**
     * @var Setup\SampleData\Executor
     */
    protected $executor;

    /**
     * @var \Magento\SalesSampleData\Setup\Installer
     */
    protected $installer;

    /**
     * @var AbstractProcessor
     */
    private $indexerProcessor;

    /**
     * InstallSalesSampleData constructor.
     * @param Setup\SampleData\Executor $executor
     * @param \Magento\SalesSampleData\Setup\Installer $installer
     * @param AbstractProcessor $indexerProcessor
     */
    public function __construct(
        Setup\SampleData\Executor $executor,
        \Magento\SalesSampleData\Setup\Installer $installer,
        \Magento\CatalogInventory\Model\Indexer\Stock\Processor $indexerProcessor
    ) {
        $this->executor = $executor;
        $this->installer = $installer;
        $this->indexerProcessor = $indexerProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->indexerProcessor->reindexAll();
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
