<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Catalog\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Product
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Product implements SetupInterface
{
    /**
     * @var string
     */
    protected $productType = \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var int
     */
    protected $attributeSetId;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $catalogConfig;

    /**
     * @var Product\Converter
     */
    protected $converter;

    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $fixtures;

    /**
     * @var Product\Gallery
     */
    protected $gallery;

    /**
     * @var \Magento\SampleData\Model\Logger
     */
    protected $logger;

    /**
     * @var \Magento\SampleData\Helper\StoreManager
     */
    protected $storeManager;

    /**
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param Product\Converter $converter
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Product\Gallery $gallery
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
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
        $fixtures = [
            'Catalog/SimpleProduct/products_gear_bags.csv',
            'Catalog/SimpleProduct/products_gear_fitness_equipment.csv',
            'Catalog/SimpleProduct/products_gear_fitness_equipment_ball.csv',
            'Catalog/SimpleProduct/products_gear_fitness_equipment_strap.csv',
            'Catalog/SimpleProduct/products_gear_watches.csv',
        ]
    ) {
        $this->productFactory = $productFactory;
        $this->catalogConfig = $catalogConfig;
        $this->converter = $converter;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->gallery = $gallery;
        $this->fixtures = $fixtures;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
    }
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log("Installing {$this->productType} products:");
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->productFactory->create();

        foreach ($this->fixtures as $file) {
            /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            foreach ($csvReader as $row) {
                /** @var \Magento\Catalog\Model\ResourceModel\Product $productResource */
                $productResource = $product->getResource();
                if ($productResource->getIdBySku($row['sku'])) {
                    continue;
                }
                $attributeSetId = $this->catalogConfig->getAttributeSetId(4, $row['attribute_set']);
                $this->converter->setAttributeSetId($attributeSetId);
                $data = $this->converter->convertRow($row);
                $product->unsetData();
                $product->setData($data);
                $product
                    ->setTypeId($this->productType)
                    ->setAttributeSetId($attributeSetId)
                    ->setWebsiteIds([$this->storeManager->getWebsiteId()])
                    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
                    ->setStockData(['is_in_stock' => 1, 'manage_stock' => 0])
                    ->setStoreId(\Magento\Store\Model\Store::DEFAULT_STORE_ID);

                if (empty($data['visibility'])) {
                    $product->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH);
                }

                $this->prepareProduct($product, $data);

                $product->save();
                $this->gallery->install($product);
                $this->logger->logInline('.');
            }
        }
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $product
     * @param array $data
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function prepareProduct($product, $data)
    {
        return $this;
    }

    /**
     * Set fixtures
     *
     * @param array $fixtures
     * @return $this
     */
    public function setFixtures(array $fixtures)
    {
        $this->fixtures = $fixtures;
        return $this;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $product
     * @return void
     */
    public function setVirtualStockData($product)
    {
        $product->setStockData(
            [
                'use_config_manage_stock' => 0,
                'is_in_stock' => 1,
                'manage_stock' => 0,
            ]
        );
    }
}
