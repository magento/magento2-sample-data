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
class InstallSampleData implements SetupInterface
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
    public function install()
    {
        $this->product->run(
            ['Magento_GiftCard::fixtures/products_giftcard.csv'],
            ['Magento_GiftCard::fixtures/GiftCard/images_giftcard.csv']
        );
    }
}
