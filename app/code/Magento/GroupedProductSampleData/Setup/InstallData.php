<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GroupedProductSampleData\Setup;

use Magento\GroupedProductSampleData\Model\Product;
use Magento\Framework\Setup;

/**
 * Launches setup of sample data for GroupedProduct module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * Setup class for grouped products
     *
     * @var Product
     */
    protected $groupedProduct;

    /**
     * @param Product $groupedProduct
     */
    public function __construct(Product $groupedProduct) {
        $this->groupedProduct = $groupedProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->groupedProduct->install(
            ['Magento_GroupedProductSampleData::fixtures/yoga_grouped.csv'],
            ['Magento_GroupedProductSampleData::fixtures/images_yoga_grouped.csv']
        );
    }
}
