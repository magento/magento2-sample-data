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
     * @var \Magento\GiftCardSampleData\Model\Product
     */
    protected $product;

    /**
     * @param \Magento\GiftCardSampleData\Model\Product $product
     */
    public function __construct(
        \Magento\GiftCardSampleData\Model\Product $product
    ) {
        $this->product = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->product->install(
            ['Magento_GiftCardSampleData::fixtures/products_giftcard.csv'],
            ['Magento_GiftCardSampleData::fixtures/images_giftcard.csv']
        );
    }
}
