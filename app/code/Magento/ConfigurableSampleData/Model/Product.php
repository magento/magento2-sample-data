<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableSampleData\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

/**
 * Setup configurable product
 */
class Product
{
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
     * @var \Magento\ImportExport\Model\ImportFactory
     */
    private $importFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param Import $importModel
     * @param \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory
     * @param \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
     * @param \Magento\ImportExport\Model\ImportFactory|null $importFactory
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        Import $importModel,
        \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        ?\Magento\ImportExport\Model\ImportFactory $importFactory = null
    ) {
        $this->eavConfig = $eavConfig;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
        $this->readFactory = $readFactory;
        $this->componentRegistrar = $componentRegistrar;
        $this->importFactory = $importFactory
            ?? ObjectManager::getInstance()->get(\Magento\ImportExport\Model\ImportFactory::class);
    }

    /**
     * @inheritdoc
     */
    public function install()
    {
        \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType::$attributeCodeToId = [];
        \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType::$commonAttributesCache = [];
        \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType::$invAttributesCache = [];
        $this->eavConfig->clear();
        /** @var Import $importModel */
        $importModel = $this->importFactory->create();
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
                'file' => 'fixtures/products.csv',
                'directory' => $this->readFactory->create(
                    $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, 'Magento_ConfigurableSampleData')
                )
            ]
        );

        $currentPath = getcwd();
        chdir(BP);
        $importModel->validateSource($source);
        $importModel->importSource();

        chdir($currentPath);

        $this->eavConfig->clear();
    }
}
