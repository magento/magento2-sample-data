<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\DownloadableSampleData\Model;

use Magento\Framework\Setup\OptionalData\Context as OptionalDataContext;

/**
 * Setup downloadable product
 */
class Product extends \Magento\CatalogSampleData\Model\Product
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

    protected $converter;

    /**
     * @param OptionalDataContext $optionalDataContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\DownloadableSampleData\Model\Product\Converter $converter
     * @param \Magento\Framework\File\Csv $csvReader
     * @param \Magento\CatalogSampleData\model\Product\Gallery $gallery
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        OptionalDataContext $optionalDataContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\DownloadableSampleData\Model\Product\Converter $converter,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\CatalogSampleData\model\Product\Gallery $gallery,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\SampleData\Helper\Deploy $deployHelper
    )
    {
        $this->deployHelper = $deployHelper;
        parent::__construct(
            $optionalDataContext,
            $productFactory,
            $catalogConfig,
            $converter,
            $csvReader,
            $gallery,
            $storeManager
        );
    }

    /**
     * {@inheritdoc}
     */
    public function run(array $productFixtures, array $galleryFixtures, array $downloadableFixtures = [])
    {
        if (!$this->deployHelper->isMediaPresent()) {
            return;
        }
        foreach ($downloadableFixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }
            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;

                $sku = $row['product_sku'];
                if (!isset($this->downloadableData[$sku])) {
                    $this->downloadableData[$sku] = [];
                }
                $this->downloadableData[$sku] =
                    $this->converter->getDownloadableData($row, $this->downloadableData[$sku]);
                $this->downloadableData[$sku]['sample'] = $this->converter->getSamplesInfo();
            }
        }

        parent::run($productFixtures, $galleryFixtures);
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
