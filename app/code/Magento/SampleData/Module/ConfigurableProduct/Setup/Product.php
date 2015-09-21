<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\ConfigurableProduct\Setup;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\SampleData\Model\SetupInterface;

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
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    private $readFactory;

    /**
     * @var \Magento\Indexer\Model\Indexer\CollectionFactory
     */
    private $indexerCollectionFactory;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\ImportExport\Model\Import $importModel
     * @param \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory
     * @param \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        \Magento\SampleData\Model\Logger $logger,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\ImportExport\Model\Import $importModel,
        \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
    ) {
        $this->logger = $logger;
        $this->eavConfig = $eavConfig;
        $this->importModel = $importModel;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
        $this->readFactory = $readFactory;
        $this->componentRegistrar = $componentRegistrar;

    }
    // @codingStandardsIgnoreEnd

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->logger->log('Installing configurable products:');

        $importModel = $this->importModel;
        $importModel->setData(
            [
                'entity' => 'catalog_product',
                'behavior' => 'append',
                'import_images_file_dir' => 'pub/media/catalog/product',
                Import::FIELD_NAME_VALIDATION_STRATEGY =>
                    ProcessingErrorAggregatorInterface::VALIDATION_STRATEGY_SKIP_ERRORS
            ]
        );

        $source = $this->csvSourceFactory->create(
            [
                'file' => 'fixtures/ConfigurableProduct/import-export_products-img.csv',
                'directory' => $this->readFactory->create(
                    $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Magento_SampleData')
                )
            ]
        );

        $currentPath = getcwd();
        chdir(BP);

        $importModel->validateSource($source);
        $importModel->importSource();

        chdir($currentPath);

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
