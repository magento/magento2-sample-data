<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\WishlistSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Installation of sample data for wishlist
 */
class Wishlist
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var Wishlist\Helper;
     */
    protected $wishlistHelper;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Framework\File\Csv $csvReader
     * @param Wishlist\Helper $wishlistHelper
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\File\Csv $csvReader,
        Wishlist\Helper $wishlistHelper,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $csvReader;
        $this->wishlistHelper = $wishlistHelper;
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function run(array $fixtures)
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getPath($fileName);
            /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
            $rows = $this->csvReader->getData($fileName);
            foreach ($rows as $row) {
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = $this->wishlistHelper->getCustomerByEmail($row['customer_email']);
                if (!$customer) {
                    continue;
                }

                /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
                $wishlist = $this->wishlistFactory->create();
                $wishlist->loadByCustomerId($customer->getId(), true);
                if (!$wishlist->getId()) {
                    continue;
                }
                $productSkuList = explode("\n", $row['product_list']);
                $this->wishlistHelper->addProductsToWishlist($wishlist, $productSkuList);
            }
        }
    }
}
