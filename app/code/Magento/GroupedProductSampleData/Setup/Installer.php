<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GroupedProductSampleData\Setup;

use Magento\Framework\Setup;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * Setup class for grouped products
     *
     * @var \Magento\GroupedProductSampleData\Model\Product
     */
    protected $groupedProduct;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param \Magento\GroupedProductSampleData\Model\Product $groupedProduct
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\GroupedProductSampleData\Model\Product $groupedProduct,
        StoreManagerInterface $storeManager = null
    ) {
        $this->groupedProduct = $groupedProduct;
        $this->storeManager = $storeManager ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(StoreManagerInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->storeManager->setCurrentStore(Store::DISTRO_STORE_ID);
        $this->groupedProduct->install(
            ['Magento_GroupedProductSampleData::fixtures/yoga_grouped.csv'],
            ['Magento_GroupedProductSampleData::fixtures/images_yoga_grouped.csv']
        );
    }
}
