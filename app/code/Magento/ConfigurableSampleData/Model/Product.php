<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ConfigurableSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Setup configurable product
 */
class Product extends \Magento\CatalogSampleData\Model\Product
{
    /**
     * @var string
     */
    protected $productType = Configurable::TYPE_CODE;

    /**
     * @var Configurable
     */
    protected $configurableProductType;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var string
     */
    protected $attributeSet;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\ConfigurableSampleData\Model\Product\Converter $converter
     * @param \Magento\Framework\File\Csv $csvReader
     * @param \Magento\CatalogSampleData\Model\Product\Gallery $gallery
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Configurable $configurableProductType
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\ConfigurableSampleData\Model\Product\Converter $converter,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\CatalogSampleData\Model\Product\Gallery $gallery,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Configurable $configurableProductType,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct(
            $sampleDataContext,
            $productFactory,
            $catalogConfig,
            $converter,
            $csvReader,
            $gallery,
            $storeManager
        );
        $this->configurableProductType = $configurableProductType;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @inheritdoc
     */
    public function install(array $productFixtures, array $galleryFixtures)
    {
        parent::install($productFixtures, $galleryFixtures);
        $this->eavConfig->clear();
    }

    /**
     * @inheritdoc
     */
    protected function installGallery($product)
    {
        parent::installGallery($product);
        foreach ($product->getAssociatedProductIds() as $id) {
            $product = $this->productFactory->create()->load($id);
            parent::installGallery($product);
        }
    }

    /**
     * @inheritdoc
     */
    protected function prepareProduct($product, $data)
    {
        if ($this->attributeSet !== $data['attribute_set']) {
            $this->attributeSet = $data['attribute_set'];
            $this->eavConfig->clear();
        }
        if (empty($data['associated_product_ids'])) {
            $simpleIds = $this->configurableProductType
                ->generateSimpleProducts($product, $data['variations_matrix']);
        } else {
            $simpleIds = $data['associated_product_ids'];
        }
        $product->setAssociatedProductIds($simpleIds);
        $product->setCanSaveConfigurableAttributes(true);
        return $this;
    }
}
