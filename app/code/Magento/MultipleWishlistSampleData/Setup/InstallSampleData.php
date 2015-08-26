<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MultipleWishlistSampleData\Setup;

use Magento\Framework\Setup;

/**
 * @codeCoverageIgnore
 */
class InstallSampleData implements SetupInterface
{
    /**
     * @var \Magento\MultipleWishlistSampleData\Model\Wishlist
     */
    private $wishlist;

    /**
     * @param \Magento\MultipleWishlistSampleData\Model\Wishlist $wishlist
     */
    public function __construct(\Magento\MultipleWishlistSampleData\Model\Wishlist $wishlist) {
        $this->wishlist = $wishlist;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->wishlist->run(['Magento_MultipleWishlistSampleData::fixtures/wishlist.csv']);
    }
}
