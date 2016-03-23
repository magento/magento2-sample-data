<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GroupedProductSampleData\Model;

use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks;
use \Magento\Framework\App\ObjectManager;

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
