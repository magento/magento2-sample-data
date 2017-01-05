<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BundleSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Bundle\Api\Data\OptionInterfaceFactory as OptionFactory;
use Magento\Bundle\Api\Data\LinkInterfaceFactory as LinkFactory;
use Magento\Catalog\Api\ProductRepositoryInterface as ProductRepository;
use \Magento\Framework\App\ObjectManager;

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
    private $optionFactory;

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
        \Magento\Eav\Model\Config $eavConfig
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
    }

    /**
     * @var LinkFactory
     */
    private $linkFactory;

    /**
     * @var ProductRepository
     */
    private $productRepository;

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
            $option = $this->getOptionFactory()->create(['data' => $optionData]);
            $option->setSku($product->getSku());
            $option->setOptionId(null);

            $links = [];
            $bundleLinks = $product->getBundleSelectionsData();
            foreach ($bundleLinks[$key] as $linkData) {
                $linkProduct = $this->getProductRepository()->getById($linkData['product_id']);
                $link = $this->getLinkFactory()->create(['data' => $linkData]);
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

    /**
     * Get option interface factory
     *
     * @deprecated
     * @return \Magento\Bundle\Api\Data\OptionInterfaceFactory
     */
    private function getOptionFactory()
    {
        if (!$this->optionFactory) {
            $this->optionFactory = ObjectManager::getInstance()->get(
                '\Magento\Bundle\Api\Data\OptionInterfaceFactory'
            );
        }
        return $this->optionFactory;
    }

    /**
     * Get bundle link interface factory
     *
     * @deprecated
     * @return \Magento\Bundle\Api\Data\LinkInterfaceFactory
     */
    private function getLinkFactory()
    {
        if (!$this->linkFactory) {
            $this->linkFactory = ObjectManager::getInstance()->get(
                '\Magento\Bundle\Api\Data\LinkInterfaceFactory'
            );
        }
        return $this->linkFactory;
    }

    /**
     * Get product repository
     *
     * @deprecated
     * @return \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private function getProductRepository()
    {
        if (!$this->productRepository) {
            $this->productRepository = ObjectManager::getInstance()->get(
                '\Magento\Catalog\Api\ProductRepositoryInterface'
            );
        }
        return $this->productRepository;
    }
}
