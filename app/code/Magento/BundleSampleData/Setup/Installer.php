<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BundleSampleData\Setup;

use Magento\Framework\Setup;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

/**
 * Launches setup of sample data for Bundle module
 */
class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * Setup class for bundle products
     *
     * @var \Magento\BundleSampleData\Model\Product
     */
    protected $bundleProduct;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\BundleSampleData\Model\Product $bundleProduct
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\BundleSampleData\Model\Product $bundleProduct,
        StoreManagerInterface $storeManager = null
    ) {
        $this->bundleProduct = $bundleProduct;
        $this->storeManager = $storeManager ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(StoreManagerInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->storeManager->setCurrentStore(Store::DISTRO_STORE_ID);
        $this->bundleProduct->install(
            ['Magento_BundleSampleData::fixtures/yoga_bundle.csv'],
            ['Magento_BundleSampleData::fixtures/images_yoga_bundle.csv']
        );
    }
}
