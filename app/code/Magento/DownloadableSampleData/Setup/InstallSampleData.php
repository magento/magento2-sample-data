<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\DownloadableSampleData\Setup;

use Magento\Framework\Setup;
/**
 * Launches setup of sample data for downloadable module
 */
class InstallSampleData implements SetupInterface
{
    /**
     * Setup class for products
     *
     * @var \Magento\DownloadableSampleData\Model\Product
     */
    protected $productSetup;

    /**
     * @param \Magento\CatalogSampleData\Module\Catalog\Setup\Category $category
     * @param \Magento\CatalogSampleData\Module\Catalog\Setup\Attribute $attribute
     * @param \Magento\DownloadableSampleData\Model\Product $product
     */
    public function __construct(
        \Magento\CatalogSampleData\Module\Catalog\Setup\Category $category,
        \Magento\CatalogSampleData\Module\Catalog\Setup\Attribute $attribute,
        \Magento\DownloadableSampleData\Model\Product $product
    ) {
        $this->category = $category;
        $this->attribute = $attribute;
        $this->downloadableProduct = $product;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->attribute->run(['Magento_DownloadableSampleData::fixtures/attributes.csv']);
        $this->category->run(['Magento_DownloadableSampleData::fixtures/categories.csv']);
        $this->downloadableProduct->run(
            ['Magento_DownloadableSampleData::fixtures/products_training_video_download.csv'],
            ['Magento_DownloadableSampleData::fixtures/images_products_training_video.csv'],
            ['Magento_DownloadableSampleData::fixtures/downloadable_data_training_video_download.csv']
        );
    }
}
