<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Wishlist;

use Magento\Framework\Setup;
use Magento\WishlistSampleData\Model\Wishlist;

/**
 * Launches setup of sample data for Wishlist module
 */
class InstallData implements Setup\InstallDataInterface
{

    /**
     * @var Wishlist
     */
    protected $wishlist;

    /**
     * @param Wishlist $wishlist
     */
    public function __construct(Wishlist $wishlist) {
        $this->wishlist = $wishlist;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->wishlist->install(['Magento_Wishlist::fixtures\wishlist.csv']);
    }
}
