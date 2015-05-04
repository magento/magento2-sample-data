<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData\Module\MultipleWishlist;

use Magento\Tools\SampleData\Helper\PostInstaller;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for MultipleWishlist module
 */
class Setup implements SetupInterface
{
    /**
     * @var PostInstaller
     */
    protected $postInstaller;

    /**
     * @var Setup\Wishlist
     */
    protected $wishlistSetup;

    /**
     * @param PostInstaller $postInstaller
     * @param Setup\Wishlist $wishlistSetup
     */
    public function __construct(
        PostInstaller $postInstaller,
        Setup\Wishlist $wishlistSetup
    ) {
        $this->postInstaller = $postInstaller;
        $this->wishlistSetup = $wishlistSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->postInstaller->removeSetupResourceType('Magento\Tools\SampleData\Module\Wishlist\Setup\Wishlist');
        $this->postInstaller->addSetupResource($this->wishlistSetup);
    }
}
