<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableSampleData\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Setup configurable product
 */
class Product
{
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
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\ImportExport\Model\Import $importModel
     * @param \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory
     * @param \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\ImportExport\Model\Import $importModel,
        \Magento\ImportExport\Model\Import\Source\CsvFactory $csvSourceFactory,
        \Magento\Indexer\Model\Indexer\CollectionFactory $indexerCollectionFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->eavConfig = $eavConfig;
        $this->importModel = $importModel;
        $this->csvSourceFactory = $csvSourceFactory;
        $this->indexerCollectionFactory = $indexerCollectionFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * @inheritdoc
     */
    public function install()
    {
        $importModel = $this->importModel;
        $importModel->setData(
            [
                'entity' => 'catalog_product',
                'behavior' => 'append',
                'import_images_file_dir' => 'pub/media/catalog/product'
            ]
        );

        $source = $this->csvSourceFactory->create(
            [
                'file' => 'Magento/ConfigurableSampleData/fixtures/products.csv',
                'directory' => $this->filesystem->getDirectoryWrite(DirectoryList::MODULES)
            ]
        );

        $currentPath = getcwd();
        chdir($this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath());

        $importModel->validateSource($source);
        $importModel->importSource();

        chdir($currentPath);

        $this->eavConfig->clear();
        $this->reindex();
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
