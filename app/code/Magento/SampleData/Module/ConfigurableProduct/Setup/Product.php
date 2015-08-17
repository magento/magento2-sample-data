<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\ConfigurableProduct\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\SetupInterface;

/**
 * Setup configurable product
 */
class Product extends \Magento\SampleData\Module\Catalog\Setup\Product implements SetupInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\VariationHandler
     */
    protected $variationHandler;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var string
     */
    protected $attributeSet;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Product\Gallery $gallery
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     * @param \Magento\ConfigurableProduct\Model\Product\VariationHandler $variationHandler
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $fixtures
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        Product\Converter $converter,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Product\Gallery $gallery,
        \Magento\SampleData\Model\Logger $logger,
        \Magento\SampleData\Helper\StoreManager $storeManager,
        \Magento\ConfigurableProduct\Model\Product\VariationHandler $variationHandler,
        \Magento\Eav\Model\Config $eavConfig,
        $fixtures = [
            'ConfigurableProduct/products_men_tops.csv',
            'ConfigurableProduct/products_men_bottoms.csv',
            'ConfigurableProduct/products_women_tops.csv',
            'ConfigurableProduct/products_women_bottoms.csv',
            'ConfigurableProduct/products_gear_fitness_equipment_ball.csv',
            'ConfigurableProduct/products_gear_fitness_equipment_strap.csv',
        ]
    ) {
        $this->eavConfig = $eavConfig;
        $this->variationHandler = $variationHandler;
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
    public function run()
    {
        parent::run();
        $this->eavConfig->clear();
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
            $simpleIds = $this->variationHandler->generateSimpleProducts($product, $data['variations_matrix']);
        } else {
            $simpleIds = $data['associated_product_ids'];
        }
        $product->setAssociatedProductIds($simpleIds);
        $product->setCanSaveConfigurableAttributes(true);
        return $this;
    }
}
