<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\DownloadableSampleData\Setup;

use Magento\Framework\Setup;
use Magento\CatalogSampleData\Model\Category;
use Magento\CatalogSampleData\Model\Attribute;
use Magento\DownloadableSampleData\Model\Product;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;

class Installer implements Setup\SampleData\InstallerInterface
{
    /**
     * @var \Magento\CatalogSampleData\Model\Category
     */
    protected $category;

    /**
     * @var Attribute
     */
    private $attribute;

    /**
     * @var Product
     */
    private $downloadableProduct;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Category  $category
     * @param Attribute $attribute
     * @param Product   $product
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Category $category,
        Attribute $attribute,
        Product $product,
        StoreManagerInterface $storeManager = null
    ) {
        $this->category = $category;
        $this->attribute = $attribute;
        $this->downloadableProduct = $product;
        $this->storeManager = $storeManager ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(StoreManagerInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->storeManager->setCurrentStore(Store::DISTRO_STORE_ID);

        $this->attribute->install(['Magento_DownloadableSampleData::fixtures/attributes.csv']);
        $this->category->install(['Magento_DownloadableSampleData::fixtures/categories.csv']);
        $this->downloadableProduct->install(
            ['Magento_DownloadableSampleData::fixtures/products_training_video_download.csv'],
            ['Magento_DownloadableSampleData::fixtures/images_products_training_video.csv'],
            ['Magento_DownloadableSampleData::fixtures/downloadable_data_training_video_download.csv']
        );
    }
}
