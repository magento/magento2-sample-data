<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MultipleWishlistSampleData\Setup;

use Magento\Framework\Setup;
use Magento\MultipleWishlistSampleData\Model\Wishlist;

/**
 * @codeCoverageIgnore
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var Wishlist
     */
    private $wishlist;

    /**
     * @param Wishlist $wishlist
     */
    public function __construct(Wishlist $wishlist) {
        $this->wishlist = $wishlist;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $context)
    {
        $this->wishlist->delete(
            [
                'Magento_WishlistSampleData::fixtures/wishlist.csv',
                'Magento_MultipleWishlistSampleData::fixtures/wishlist.csv'
            ]
        );
        $this->wishlist->install(
            [
                'Magento_WishlistSampleData::fixtures/wishlist.csv',
                'Magento_MultipleWishlistSampleData::fixtures/wishlist.csv'
            ]
        );
    }
}
