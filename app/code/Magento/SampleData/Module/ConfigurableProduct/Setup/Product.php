<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\ConfigurableProduct\Setup;

use Magento\SampleData\Model\SetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Setup configurable product
 */
class Product implements SetupInterface
{
    /**
     * @var \Magento\SampleData\Model\Logger
     */
    private $logger;

    /**
     * @var \Magento\ImportExport\Model\Import
     */
    private $importModel;

    /**
     * @var \Magento\ImportExport\Model\Import\Source\CsvFactory
     */
    private $csvSourceFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    private $filesystem;

    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    private $indexerCollectionFactory;

    /**
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\ImportExport\Model\Import $importModel
     * @param \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory
     * @param \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        \Magento\SampleData\Model\Logger $logger,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\ImportExport\Model\Import $importModel,
        \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->logger = $logger;
        $this->eavConfig = $eavConfig;
        $this->importModel = $importModel;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
        $this->filesystem = $filesystem;

    }
    // @codingStandardsIgnoreEnd

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->logger->log('Installing configurable products:');

        $importModel = $this->importModel;
        $importModel->setData(['entity' => 'catalog_product', 'behavior' => 'append']);

        $source = $this->csvSourceFactory->create(
            [
                'file' => 'Magento/SampleData/fixtures/ConfigurableProduct/import-export_products-img.csv',
                'directory' => $this->filesystem->getDirectoryWrite(DirectoryList::MODULES)
            ]
        );

        $importModel->validateSource($source);
        $importModel->importSource();
        $this->logger->logInline('.');

        $this->eavConfig->clear();
        $this->reindex();

        $this->logger->logInline('.');
    }

    /**
     * Perform full reindex
     */
    private function reindex()
    {
        foreach ($this->indexerCollectionFactory->create()->getItems() as $indexer) {
            $indexer->reindexAll();
        }
    }
}
