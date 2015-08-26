<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Module\Wishlist;

use Magento\WishlistSampleData\Model\Wishlist;
use Magento\Framework\Setup;
/**
 * Launches setup of sample data for Wishlist module
 */
class InstallSampleData implements SetupInterface
{

    /**
     * @var Wishlist
     */
    protected $wishlist;

    /**
     * @param Wishlist $wishlist
     */
    public function __construct(
        Wishlist $wishlist
    ) {
        $this->wishlist = $wishlist;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->wishlist->run(['Magento_Wishlist::fixtures\wishlist.csv']);
    }
}
