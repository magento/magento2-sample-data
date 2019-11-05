<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MsrpSampleData\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use \Magento\Msrp\Model\Product\Attribute\Source\Type;
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
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     * @deprecated
     * @see $collectionFactory
     */
    protected $productCollection;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->productCollection = $productCollectionFactory->create()->addAttributeToSelect('sku');
        $this->configWriter = $configWriter;
        $this->collectionFactory = $productCollectionFactory;
    }

    /**
     * Load products with given SKUs.
     *
     * @param string[] $skus
     * @return \Magento\Catalog\Model\Product[]
     */
    private function loadProducts(array $skus): array
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('sku');
        $collection->addAttributeToFilter('sku', ['in' => $skus]);
        $products = [];
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($collection as $product) {
            $products[$product->getSku()] = $product;
        }

        return $products;
    }

    /**
     * {@inheritdoc}
     */
    public function install(array $fixtures)
    {
        $this->configWriter->save('sales/msrp/enabled', 1);
        foreach ($fixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }

            $productsData = [];
            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);
            foreach ($rows as $row) {
                $rowData = [];
                $sku = null;
                foreach ($row as $i => $data) {
                    if ($header[$i] === 'sku') {
                        $sku = $data;
                    } else {
                        $rowData[$header[$i]] = $data;
                    }
                }
                if ($sku) {
                    $productsData[$sku] = $rowData;
                }
            }
            if ($productsData) {
                $products = $this->loadProducts(array_keys($productsData));

                foreach ($productsData as $sku => $data) {
                    if (!array_key_exists($sku, $products)) {
                        throw new \RuntimeException('Require product with SKU#' . $sku .' not found!');
                    }
                    $product = $products[$sku];
                    $product->setMsrpDisplayActualPriceType(Type::TYPE_ON_GESTURE);
                    if (!empty($data['msrp'])) {
                        $price = $data['msrp'];
                    } else {
                        $price = $product->getPrice()*1.1;
                    }
                    $product->setMsrp($price);
                    $product->save();
                }
            }
        }
    }

    /**
     * Retrieve product ID by sku
     *
     * @param string $sku
     * @return int|null
     * @deprecated
     * @see loadProducts()
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
