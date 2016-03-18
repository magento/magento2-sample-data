<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BundleSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Bundle\Api\Data\OptionInterfaceFactory as OptionFactory;
use Magento\Bundle\Api\Data\LinkInterfaceFactory as LinkFactory;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;

/**
 * Setup bundle product
 */
class Product extends \Magento\CatalogSampleData\Model\Product
{
    /**
     * @var string
     */
    protected $productType = \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE;

    /**
     * @var OptionFactory
     */
    protected $optionFactory;

    /**
     * @var LinkFactory
     */
    protected $linkFactory;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Product constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\ConfigFactory $catalogConfig
     * @param Product\Converter $converter
     * @param \Magento\CatalogSampleData\Model\Product\Gallery $gallery
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ConfigFactory $catalogConfig,
        \Magento\BundleSampleData\Model\Product\Converter $converter,
        \Magento\CatalogSampleData\Model\Product\Gallery $gallery,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig,
        OptionFactory $optionFactory,
        LinkFactory $linkFactory,
        ProductRepository $productRepository
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct(
            $sampleDataContext,
            $productFactory,
            $catalogConfig,
            $converter,
            $gallery,
            $storeManager,
            $eavConfig
        );
        $this->optionFactory = $optionFactory;
        $this->linkFactory = $linkFactory;
        $this->productRepository = $productRepository;
    }

    /**
     * @inheritdoc
     */
    protected function prepareProduct($product, $data)
    {
        $product
            ->setCanSaveConfigurableAttributes(true)
            ->setCanSaveBundleSelections(true)
            ->setPriceType(0);
        $bundleOptionsData = $product->getBundleOptionsData();
        $options = [];
        foreach ($bundleOptionsData as $key => $optionData) {

            $option = $this->optionFactory->create(['data' => $optionData]);
            $option->setSku($product->getSku());
            $option->setOptionId(null);

            $links = [];
            $bundleLinks = $product->getBundleSelectionsData();
            foreach ($bundleLinks[$key] as $linkData) {
                $linkProduct = $this->productRepository->getById($linkData['product_id']);
                $link = $this->linkFactory->create(['data' => $linkData]);
                $link->setSku($linkProduct->getSku());
                $link->setQty($linkData['selection_qty']);

                if (array_key_exists('selection_can_change_qty', $linkData)) {
                    $link->setCanChangeQuantity($linkData['selection_can_change_qty']);
                }
                $links[] = $link;
            }
            $option->setProductLinks($links);
            $options[] = $option;
        }

        $extension = $product->getExtensionAttributes();
        $extension->setBundleProductOptions($options);
        $product->setExtensionAttributes($extension);

        return $this;
    }
}
