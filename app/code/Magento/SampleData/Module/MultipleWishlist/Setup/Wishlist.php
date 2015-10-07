<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\MultipleWishlist\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\Logger;
use Magento\SampleData\Model\SetupInterface;

/**
 * Installation of sample data for multiple wishlist
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
     * @var \Magento\SampleData\Module\Wishlist\Setup\Wishlist\Helper
     */
    protected $wishlistHelper;

    /**
     * @var \Magento\MultipleWishlist\Model\WishlistEditor
     */
    protected $wishlistEditor;

    /**
     * @var \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory
     */
    protected $wishlistColFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $configCacheType;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\SampleData\Module\Wishlist\Setup\Wishlist\Helper $wishlistHelper
     * @param \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
     * @param \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlistColFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param Logger $logger
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\SampleData\Module\Wishlist\Setup\Wishlist\Helper $wishlistHelper,
        \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor,
        \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlistColFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        Logger $logger
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->wishlistHelper = $wishlistHelper;
        $this->wishlistEditor = $wishlistEditor;
        $this->wishlistColFactory = $wishlistColFactory;
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->configCacheType = $configCacheType;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing multiple wishlists:');
        $multipleEnabledConfig = 'wishlist/general/multiple_enabled';
        if (!$this->config->isSetFlag($multipleEnabledConfig)) {
            $this->configWriter->save($multipleEnabledConfig, 1);
            $this->configCacheType->clean();
        }

        $fixtureFiles = ['Wishlist/wishlist.csv', 'MultipleWishlist/wishlist.csv'];
        foreach ($fixtureFiles as $fixtureFile) {
            $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
            /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
            foreach ($csvReader as $row) {
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = $this->wishlistHelper->getCustomerByEmail($row['customer_email']);
                if (!$customer) {
                    continue;
                }

                $wishlistName = $row['name'];
                /** @var \Magento\Wishlist\Model\ResourceModel\Wishlist\Collection $wishlistCollection */
                $wishlistCollection = $this->wishlistColFactory->create();
                $wishlistCollection->filterByCustomerId($customer->getId())->addFieldToFilter('name', $wishlistName);
                /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
                $wishlist = $wishlistCollection->fetchItem();
                if ($wishlist) {
                    continue;
                }
                $wishlist = $this->wishlistEditor->edit($customer->getId(), $wishlistName, true);
                if (!$wishlist->getId()) {
                    continue;
                }
                $productSkuList = explode("\n", $row['product_list']);
                $this->wishlistHelper->addProductsToWishlist($wishlist, $productSkuList);
                $this->logger->logInline('.');
            }
        }
    }
}
