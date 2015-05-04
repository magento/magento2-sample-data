<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\ConfigurableProduct;

use Magento\SampleData\Model\SetupInterface;

/**
 * Class Setup
 */
class Setup implements SetupInterface
{
    /**
     * @var Setup\Product
     */
    protected $productSetup;

    /**
     * @param Setup\Product $productSetup
     */
    public function __construct(
        Setup\Product $productSetup
    ) {
        $this->productSetup = $productSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->productSetup->run();
    }
}
