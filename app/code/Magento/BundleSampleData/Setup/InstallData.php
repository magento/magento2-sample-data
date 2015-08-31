<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\BundleSampleData\Setup;

use Magento\Framework\Setup;

/**
 * Launches setup of sample data for Bundle module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * Setup class for bundle products
     *
     * @var \Magento\BundleSampleData\Model\Product
     */
    protected $bundleProduct;

    /**
     * @param \Magento\BundleSampleData\Model\Product $bundleProduct
     */
    public function __construct(
        \Magento\BundleSampleData\Model\Product $bundleProduct
    ) {
        $this->bundleProduct = $bundleProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->bundleProduct->install(
            ['Magento_BundleSampleData::fixtures/yoga_bundle.csv'],
            ['Magento_BundleSampleData::fixtures/images_yoga_bundle.csv']
        );
    }
}
