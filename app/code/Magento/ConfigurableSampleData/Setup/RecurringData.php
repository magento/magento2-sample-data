<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\ConfigurableSampleData\Setup;

use Magento\Framework\App\State;
use Magento\Framework\Indexer\IndexerInterfaceFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Recurring data install.
 */
class RecurringData implements InstallDataInterface
{
    /**
     * @var State
     */
    private $state;

    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    private $indexerCollectionFactory;

    /**
     * Init
     *
     * @param IndexerInterfaceFactory $indexerInterfaceFactory
     */
    public function __construct(
        State $state,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory
    ) {
        $this->state = $state;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->state->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_CRONTAB,
            [$this, 'reindex']
        );
    }

    /**
     * Perform full reindex
     */
    public function reindex()
    {
        foreach ($this->indexerCollectionFactory->create()->getItems() as $indexer) {
            $indexer->reindexAll();
        }
    }
}
