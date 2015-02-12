<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData\Module\Bundle;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for Bundle module
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for bundle products
     *
     * @var Setup\Product
     */
    protected $bundleProduct;

    /**
     * @param Setup\Product $bundleProduct
     */
    public function __construct(
        Setup\Product $bundleProduct
    ) {
        $this->bundleProduct = $bundleProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->bundleProduct->run();
    }
}
