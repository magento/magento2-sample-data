<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Downloadable;

/**
 * Launches setup of sample data for downloadable module
 */
class Setup extends \Magento\SampleData\Module\Catalog\Setup
{
    /**
     * Setup class for products
     *
     * @var Setup\Product
     */
    protected $productSetup;

    /**
     * @param \Magento\SampleData\Module\Catalog\Setup\Category $categorySetup
     * @param \Magento\SampleData\Module\Catalog\Setup\Attribute $attributeSetup
     * @param Setup\Product $productSetup
     */
    public function __construct(
        \Magento\SampleData\Module\Catalog\Setup\Category $categorySetup,
        \Magento\SampleData\Module\Catalog\Setup\Attribute $attributeSetup,
        Setup\Product $productSetup
    ) {
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
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
