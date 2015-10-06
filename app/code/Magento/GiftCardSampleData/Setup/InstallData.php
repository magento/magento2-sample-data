<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardSampleData\Setup;

use Magento\Framework\Setup;

/**
 * Class Setup
 * Launches setup of sample data for GiftCard module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * Setup class for category
     *
     * @var \Magento\CatalogSampleData\Model\Category
     */
    protected $categorySetup;

    /**
     * Setup class for product attributes
     *
     * @var \Magento\CatalogSampleData\Model\Attribute
     */
    protected $attributeSetup;

    /**
     * @var \Magento\ProductLinksSampleData\Model\ProductLink
     */
    protected $productLinkSetup;

    /**
     * @var \Magento\GiftCardSampleData\Model\Product
     */
    protected $product;

    /**
     * @param \Magento\CatalogSampleData\Model\Category $categorySetup
     * @param \Magento\CatalogSampleData\Model\Attribute $attributeSetup
     * @param \Magento\GiftCardSampleData\Model\Product $product
     * @param \Magento\ProductLinksSampleData\Model\ProductLink $productLinkSetup
     */
    public function __construct(
        \Magento\CatalogSampleData\Model\Category $categorySetup,
        \Magento\CatalogSampleData\Model\Attribute $attributeSetup,
        \Magento\GiftCardSampleData\Model\Product $product,
        \Magento\ProductLinksSampleData\Model\ProductLink $productLinkSetup
    ) {
        $this->product = $product;
        $this->attributeSetup = $attributeSetup;
        $this->categorySetup = $categorySetup;
        $this->productLinkSetup = $productLinkSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->attributeSetup->install(['Magento_GiftCardSampleData::fixtures/attributes.csv']);
        $this->categorySetup->install(['Magento_GiftCardSampleData::fixtures/categories.csv']);
        $this->product->install(
            ['Magento_GiftCardSampleData::fixtures/products_giftcard.csv'],
            ['Magento_GiftCardSampleData::fixtures/images_giftcard.csv']
        );
        $this->productLinkSetup->install(
            ['Magento_GiftCardSampleData::fixtures/related.csv'],
            [],
            ['Magento_GiftCardSampleData::fixtures/crossell.csv']
        );
    }
}
