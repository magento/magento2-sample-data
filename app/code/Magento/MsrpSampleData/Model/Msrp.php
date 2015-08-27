<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MsrpSampleData\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Class Msrp
 *
 */
class Msrp
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
     * @var array
     */
    protected $productIds;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Collection
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
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Framework\File\Csv $csvReader
     * @param \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\SampleData\Model\Logger $logger
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Framework\File\Csv $csvReader,
        \Magento\Catalog\Model\Resource\Product\CollectionFactory $productCollectionFactory,
        \Magento\SampleData\Model\Logger $logger,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $csvReader;
        $this->productCollection = $productCollectionFactory->create()->addAttributeToSelect('sku');
        $this->logger = $logger;
        $this->configWriter = $configWriter;
    }

    /**
     * {@inheritdoc}
     */
    public function run(array $fixtures)
    {
        $this->configWriter->save('sales/msrp/enabled', 1);
        foreach ($fixtures as $fileName) {
            $fixtureFile = $this->fixtureManager->getPath($fileName);
            /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
            $csvReader = $this->csvReader->getData($fixtureFile);
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
