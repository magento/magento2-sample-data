<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\DownloadableSampleData\Model;

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
     * @var \Magento\DownloadableSampleData\Model\Product\Converter $converter
     */
    protected $converter;

    /**
     * @var array
     */
    protected $downloadableData = [];

    /**
     * {@inheritdoc}
     */
    public function install(array $productFixtures, array $galleryFixtures, array $downloadableFixtures = [])
    {
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

        parent::install($productFixtures, $galleryFixtures);
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
