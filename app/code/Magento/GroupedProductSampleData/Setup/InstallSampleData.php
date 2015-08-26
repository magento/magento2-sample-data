<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\GroupedProductSampleData\Setup;

use Magento\Framework\Setup;

/**
 * Launches setup of sample data for GroupedProduct module
 */
class InstallSampleData implements SetupInterface
{
    /**
     * Setup class for grouped products
     *
     * @var \Magento\GroupedProductSampleData\Model\Product
     */
    protected $groupedProduct;

    /**
     * @param \Magento\GroupedProductSampleData\Model\Product $groupedProduct
     */
    public function __construct(
        \Magento\GroupedProductSampleData\Model\Product $groupedProduct
    ) {
        $this->groupedProduct = $groupedProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->groupedProduct->run(
            ['Magento_GroupedProduct::GroupedProduct/yoga_grouped.csv'],
            ['Magento_GroupedProduct/images_yoga_grouped.csv']
        );
    }
}
