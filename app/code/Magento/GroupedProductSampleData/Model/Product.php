<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GroupedProductSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks;
use Magento\Framework\App\ObjectManager;

/**
 * Setup grouped product
 */
class Product extends \Magento\CatalogSampleData\Model\Product
{
    /**
     * @var string
     */
    protected $productType = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;

    /**
     * @var \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks
     */
    private $productLinksHelper;

    /**
     * Product constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\ConfigFactory $catalogConfig
     * @param \Magento\GroupedProductSampleData\Model\Product\Converter $converter
     * @param \Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager
     * @param \Magento\CatalogSampleData\Model\Product\Gallery $gallery
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ConfigFactory $catalogConfig,
        \Magento\GroupedProductSampleData\Model\Product\Converter $converter,
        \Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager,
        \Magento\CatalogSampleData\Model\Product\Gallery $gallery,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct(
            $sampleDataContext,
            $productFactory,
            $catalogConfig,
            $converter,
            $gallery,
            $storeManager,
            $eavConfig
        );
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     * @return $this
     */
    protected function prepareProduct($product, $data)
    {
        $this->getProductLinksHelper()->initializeLinks($product, $data['grouped_link_data']);
        $product->unsetData('grouped_link_data');
        return $this;
    }

    /**
     * Get product links helper
     *
     * @deprecated
     * @return \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks
     */
    private function getProductLinksHelper()
    {

        if (!($this->productLinksHelper)) {
            return ObjectManager::getInstance()->get(
                '\Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks'
            );
        } else {
            return $this->productLinksHelper;
        }
    }
}
