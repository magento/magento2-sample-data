<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftCardSampleData\Model;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\SampleData\Model\SetupInterface;

/**
 * Setup Gift Card
 */
class Product extends \Magento\CatalogSampleData\Model\Product implements SetupInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\GiftCardSampleData\Model\Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Gallery $gallery
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Gallery $gallery,
        \Magento\SampleData\Helper\StoreManager $storeManager
    ) {
        parent::__construct(
            $productFactory,
            $catalogConfig,
            $converter,
            $fixtureHelper,
            $csvReaderFactory,
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
