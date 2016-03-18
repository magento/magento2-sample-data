<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\BundleSampleData\Model;

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

        if (!($this->optionFactory)) {
            return ObjectManager::getInstance()->get(
                '\Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks'
            );
        } else {
            return $this->optionFactory;
        }
    }

    /**
     * Get bundle link interface factory
     *
     * @deprecated
     * @return \Magento\Bundle\Api\Data\LinkInterfaceFactory
     */
    private function getLinkFactory()
    {

        if (!($this->linkFactory)) {
            return ObjectManager::getInstance()->get(
                '\Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks'
            );
        } else {
            return $this->linkFactory;
        }
    }

    /**
     * Get product repository
     *
     * @deprecated
     * @return \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private function getProductRepository()
    {

        if (!($this->productRepository)) {
            return ObjectManager::getInstance()->get(
                '\Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks'
            );
        } else {
            return $this->productRepository;
        }
    }
}
