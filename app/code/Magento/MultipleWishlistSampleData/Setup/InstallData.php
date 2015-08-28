<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\MultipleWishlistSampleData\Setup;

use Magento\Framework\Setup;
use Magento\MultipleWishlistSampleData\Model;

/**
 * @codeCoverageIgnore
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var Model\Wishlist
     */
    private $wishlist;

    /**
     * @param Model\Wishlist $wishlist
     */
    public function __construct(Model\Wishlist $wishlist) {
        $this->wishlist = $wishlist;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $context)
    {
        $this->wishlist->install(
            [
                'Magento_MultipleWishlistSampleData::fixtures/wishlist.csv',
            ]
        );
    }
}
