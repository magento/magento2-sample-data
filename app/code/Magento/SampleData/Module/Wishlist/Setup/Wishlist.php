<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Wishlist\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\Logger;
use Magento\SampleData\Model\SetupInterface;

/**
 * Installation of sample data for wishlist
 */
class Wishlist implements SetupInterface
{
    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var Wishlist\Helper;
     */
    protected $wishlistHelper;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Wishlist\Helper $wishlistHelper
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param Logger $logger
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Wishlist\Helper $wishlistHelper,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        Logger $logger
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->wishlistHelper = $wishlistHelper;
        $this->wishlistFactory = $wishlistFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing wishlists:');

        $fixtureFile = 'Wishlist/wishlist.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $row) {
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
            $this->logger->logInline('.');
        }
    }
}
