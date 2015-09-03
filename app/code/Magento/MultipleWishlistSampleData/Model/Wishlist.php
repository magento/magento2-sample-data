<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MultipleWishlistSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Installation of sample data for multiple wishlist
 */
class Wishlist
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\WishlistSampleData\Model\Helper
     */
    protected $helper;

    /**
     * @var \Magento\MultipleWishlist\Model\WishlistEditor
     */
    protected $wishlistEditor;

    /**
     * @var \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory
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
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\WishlistSampleData\Model\Helper $wishlistHelper
     * @param \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor
     * @param \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistColFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\WishlistSampleData\Model\Helper $wishlistHelper,
        \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor,
        \Magento\Wishlist\Model\Resource\Wishlist\CollectionFactory $wishlistColFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Cache\Type\Config $configCacheType
    ) {
        $this->fixtureHelper = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->helper = $wishlistHelper;
        $this->wishlistEditor = $wishlistEditor;
        $this->wishlistColFactory = $wishlistColFactory;
        $this->config = $config;
        $this->configWriter = $configWriter;
        $this->configCacheType = $configCacheType;
    }

    /**
     * {@inheritdoc}
     */
    public function install(array $fixtures)
    {
        $multipleEnabledConfig = 'wishlist/general/multiple_enabled';
        if (!$this->config->isSetFlag($multipleEnabledConfig)) {
            $this->configWriter->save($multipleEnabledConfig, 1);
            $this->configCacheType->clean();
        }

        foreach ($fixtures as $fixture) {
            $fileName = $this->fixtureHelper->getFixture($fixture);
            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = $this->helper->getCustomerByEmail($row['customer_email']);
                if (!$customer) {
                    continue;
                }

                $wishlistName = $row['name'];
                $wishlist = $this->wishlistEditor->edit($customer->getId(), $wishlistName, true);
                if (!$wishlist->getId()) {
                    continue;
                }
                $productSkuList = explode("\n", $row['product_list']);
                $this->helper->addProductsToWishlist($wishlist, $productSkuList);
            }
        }
    }

    public function delete($fixtures)
    {
        foreach ($fixtures as $fixture) {
            $fileName = $this->fixtureHelper->getFixture($fixture);
            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;
                /** @var \Magento\Customer\Model\Customer $customer */
                $customer = $this->helper->getCustomerByEmail($row['customer_email']);
                if (!$customer) {
                    continue;
                }

                /** @var \Magento\Wishlist\Model\Resource\Wishlist\Collection $wishlistCollection */
                $wishlistCollection = $this->wishlistColFactory->create();
                $wishlistCollection->filterByCustomerId($customer->getId());
                /** @var \Magento\Wishlist\Model\Wishlist $wishlist */
                foreach ($wishlistCollection as $wishlist) {
                    $wishlist->delete();
                }
            }
        }
    }
}
