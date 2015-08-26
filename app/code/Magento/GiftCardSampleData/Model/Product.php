<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Setup Gift Card
 */
class Product extends \Magento\CatalogSampleData\Model\Product
{
    /**
     * @var string
     */
    protected $productType = \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\GiftCardSampleData\Model\Product\Converter $converter
     * @param \Magento\Framework\File\Csv $csvReader
     * @param \Magento\CatalogSampleData\model\Product\Gallery $gallery
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\GiftCardSampleData\Model\Product\Converter $converter,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\CatalogSampleData\model\Product\Gallery $gallery,
        \Magento\SampleData\Helper\StoreManager $storeManager
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
    }

    /**
     * @inheritdoc
     */
    protected function prepareProduct($product, $data)
    {
        if ($product->getGiftcardType() == \Magento\GiftCard\Model\Giftcard::TYPE_VIRTUAL) {
            $this->setVirtualStockData($product);
        }
        return $this;
    }
}
