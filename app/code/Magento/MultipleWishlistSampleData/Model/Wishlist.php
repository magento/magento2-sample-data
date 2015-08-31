<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MultipleWishlistSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

class Wishlist
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    private $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Wishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var Helper
     */
    protected $wishlistHelper;

    /**
     * @param SampleDataContext $optionalDataContext
     * @param \Magento\Framework\File\Csv $csvReader
     * @param Helper $wishlistHelper
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     */
    public function __construct(
        SampleDataContext $optionalDataContext,
        \Magento\Framework\File\Csv $csvReader,
        Helper $wishlistHelper,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
    ) {
        $this->fixtureManager = $optionalDataContext->getFixtureManager();
        $this->csvReader = $csvReader;
        $this->wishlistHelper = $wishlistHelper;
        $this->wishlistFactory = $wishlistFactory;
    }

    public function install(array $fixtures)
    {
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getPath($fileName);
            if (!file_exists($fileName)) {
                continue;
            }
            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;

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
