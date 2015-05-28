<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Catalog;

use Magento\SampleData\Helper\PostInstaller;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Setup
 * Launches setup of sample data for catalog module
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for category
     *
     * @var Setup\Category
     */
    protected $categorySetup;

    /**
     * Setup class for product attributes
     *
     * @var Setup\Attribute
     */
    protected $attributeSetup;

    /**
     * Setup class for products
     *
     * @var Setup\Product
     */
    protected $productSetup;

    /**
     * Setup class for products
     *
     * @var Setup\ProductLink
     */
    protected $productLinkSetup;

    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @param Setup\Category $categorySetup
     * @param Setup\Attribute $attributeSetup
     * @param Setup\Product $productSetup
     * @param Setup\ProductLink $productLinkSetup
     * @param PostInstaller $postInstaller
     */
    public function __construct(
        Setup\Category $categorySetup,
        Setup\Attribute $attributeSetup,
        Setup\Product $productSetup,
        Setup\ProductLink $productLinkSetup,
        PostInstaller $postInstaller
    ) {
        $this->categorySetup = $categorySetup;
        $this->attributeSetup = $attributeSetup;
        $this->productSetup = $productSetup;
        $this->productLinkSetup = $productLinkSetup;
        $this->postInstaller = $postInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->attributeSetup->run();
        $this->categorySetup->run();
        $this->productSetup->run();
        $this->postInstaller->addSetupResource($this->productLinkSetup);
    }
}
