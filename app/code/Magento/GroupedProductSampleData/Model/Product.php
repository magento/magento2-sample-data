<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GroupedProductSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Setup grouped product
 */
class Product extends \Magento\CatalogSampleData\Model\Product
{
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var string
     */
    protected $productType = \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\GroupedProductSampleData\Model\Product\Converter $converter
     * @param \Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager
     * @param \Magento\Framework\File\Csv $csvReader
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     */
    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\GroupedProductSampleData\Model\Product\Converter $converter,
        \Magento\Framework\Setup\SampleData\FixtureManager $fixtureManager,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\SampleData\Helper\StoreManager $storeManager
    )
    {
        parent::__construct(
            $productFactory,
            $catalogConfig,
            $converter,
            $fixtureManager,
            $csvReader,
            $storeManager
        );
    }
}
