<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Downloadable\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\SampleData\Model\SetupInterface;

/**
 * Setup downloadable product
 */
class Product extends \Magento\SampleData\Module\Catalog\Setup\Product implements SetupInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE;

    /**
     * @var \Magento\SampleData\Helper\Deploy
     */
    protected $deployHelper;

    /**
     * @var array
     */
    protected $downloadableData = [];

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Gallery $gallery
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     * @param \Magento\SampleData\Helper\Deploy $deployHelper
     * @param array $fixtures
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Gallery $gallery,
        \Magento\SampleData\Model\Logger $logger,
        \Magento\SampleData\Helper\StoreManager $storeManager,
        \Magento\SampleData\Helper\Deploy $deployHelper,
        $fixtures = ['Downloadable/products_training_video_download.csv']
    ) {
        $this->deployHelper = $deployHelper;
        parent::__construct(
            $productFactory,
            $catalogConfig,
            $converter,
            $fixtureHelper,
            $csvReaderFactory,
            $gallery,
            $logger,
            $storeManager,
            $fixtures
        );
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if (!$this->deployHelper->isMediaPresent()) {
            $this->logger->log('Sample Data Media was not installed. Skipping downloadable product installation');
            return;
        }
        $this->gallery->setFixtures([
                'Downloadable/images_products_training_video.csv',
        ]);
        $downloadableFiles = [
            'Downloadable/downloadable_data_training_video_download.csv',
        ];
        foreach ($downloadableFiles as $downloadableFile) {
            $downloadableFileName = $this->fixtureHelper->getPath($downloadableFile);
            $csvDownloadableReader = $this->csvReaderFactory
                ->create(['fileName' => $downloadableFileName, 'mode' => 'r']);
            foreach ($csvDownloadableReader as $downloadableRow) {
                $sku = $downloadableRow['product_sku'];
                if (!isset($this->downloadableData[$sku])) {
                    $this->downloadableData[$sku] = [];
                }
                $this->downloadableData[$sku] = $this->converter->getDownloadableData(
                    $downloadableRow,
                    $this->downloadableData[$sku]
                );
                $this->downloadableData[$sku]['sample'] = $this->converter->getSamplesInfo();
            }
        }

        parent::run();
    }

    /**
     * @inheritdoc
     */
    protected function prepareProduct($product, $data)
    {
        if (isset($this->downloadableData[$data['sku']])) {
            $product->setDownloadableData($this->downloadableData[$data['sku']]);
        }
        $this->setVirtualStockData($product);
        return $this;
    }
}
