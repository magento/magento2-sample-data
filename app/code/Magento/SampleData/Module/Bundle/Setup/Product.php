<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Bundle\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Module\Catalog\Setup\Product\Gallery;
use Magento\SampleData\Model\SetupInterface;

/**
 * Setup bundle product
 */
class Product extends \Magento\SampleData\Module\Catalog\Setup\Product implements SetupInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Gallery $gallery
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     * @param array $fixtures
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Gallery $gallery,
        \Magento\SampleData\Model\Logger $logger,
        \Magento\SampleData\Helper\StoreManager $storeManager,
        $fixtures = [
            'Bundle/yoga_bundle.csv',
        ]
    ) {
        $gallery->setFixtures([
            'Bundle/images_yoga_bundle.csv',
        ]);
        parent::__construct(
            $productFactory,
            $catalogConfig,
            $converter,
            $fixtureHelper,
            $csvReaderFactory,
            $gallery,
            $logger,
            $storeManager,
            $fixtures
        );
    }
    // @codingStandardsIgnoreEnd

    /**
     * @inheritdoc
     */
    protected function prepareProduct($product, $data)
    {
        $product->setCanSaveConfigurableAttributes(true)
            ->setCanSaveBundleSelections(true)
            ->setPriceType(0);

        return $this;
    }
}
