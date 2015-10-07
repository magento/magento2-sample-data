<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Msrp\Setup;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Msrp
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Msrp implements SetupInterface
{
    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var array
     */
    protected $productIds;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected $productCollection;

    /**
     * @var \Magento\SampleData\Model\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\SampleData\Model\Logger $logger,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->productCollection = $productCollectionFactory->create()->addAttributeToSelect('sku');
        $this->logger = $logger;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing MAP:');
        $this->configWriter->save('sales/msrp/enabled', 1);

        $fixtureFile = 'Msrp/products_msrp.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $row) {
            $productId = $this->getProductIdBySku($row['sku']);
            if (!$productId) {
                continue;
            }
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productCollection->getItemById($productId);
            $product->setMsrpDisplayActualPriceType(\Magento\Msrp\Model\Product\Attribute\Source\Type::TYPE_ON_GESTURE);
            if (!empty($row['msrp'])) {
                $price = $row['msrp'];
            } else {
                $price = $product->getPrice()*1.1;
            }
            $product->setMsrp($price);
            $product->save();
            $this->logger->logInline('.');
        }
    }

    /**
     * Retrieve product ID by sku
     *
     * @param string $sku
     * @return int|null
     */
    protected function getProductIdBySku($sku)
    {
        if (empty($this->productIds)) {
            /** @var \Magento\Catalog\Model\Product $product */
            foreach ($this->productCollection as $product) {
                $this->productIds[$product->getSku()] = $product->getId();
            }
        }
        if (isset($this->productIds[$sku])) {
            return $this->productIds[$sku];
        }
        return null;
    }
}
