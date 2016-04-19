<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\DownloadableSampleData\Model;

use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Downloadable\Api\Data\SampleInterfaceFactory as SampleFactory;
use Magento\Downloadable\Api\Data\LinkInterfaceFactory as LinkFactory;
use \Magento\Framework\App\ObjectManager;

/**
 * Setup downloadable product
 */
class Product extends \Magento\CatalogSampleData\Model\Product
{
    /**
     * @var string
     */
    protected $productType = \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE;

    /**
     * @var \Magento\DownloadableSampleData\Model\Product\Converter $converter
     */
    protected $converter;

    /**
     * @var array
     */
    protected $downloadableData = [];

    /**
     * @var SampleFactory
     */
    protected $sampleFactory;

    /**
     * @var LinkFactory
     */
    protected $linkFactory;

    /**
     * Product constructor.
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\ConfigFactory $catalogConfig
     * @param Product\Converter $converter
     * @param \Magento\CatalogSampleData\Model\Product\Gallery $gallery
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ConfigFactory $catalogConfig,
        \Magento\DownloadableSampleData\Model\Product\Converter $converter,
        \Magento\CatalogSampleData\Model\Product\Gallery $gallery,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        parent::__construct(
            $sampleDataContext,
            $productFactory,
            $catalogConfig,
            $converter,
            $gallery,
            $storeManager,
            $eavConfig
        );
    }

    /**
     * {@inheritdoc}
     */
    public function install(array $productFixtures, array $galleryFixtures, array $downloadableFixtures = [])
    {
        foreach ($downloadableFixtures as $fileName) {
            $fileName = $this->fixtureManager->getFixture($fileName);
            if (!file_exists($fileName)) {
                continue;
            }
            $rows = $this->csvReader->getData($fileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $row = $data;

                $sku = $row['product_sku'];
                if (!isset($this->downloadableData[$sku])) {
                    $this->downloadableData[$sku] = [];
                }
                $this->downloadableData[$sku] =
                    $this->converter->getDownloadableData($row, $this->downloadableData[$sku]);
                $this->downloadableData[$sku]['sample'] = $this->converter->getSamplesInfo();
            }
        }

        parent::install($productFixtures, $galleryFixtures);
    }

    /**
     * @inheritdoc
     */
    protected function prepareProduct($product, $data)
    {
        if (isset($this->downloadableData[$data['sku']])) {
            $extension = $product->getExtensionAttributes();
            $links = [];
            foreach ($this->downloadableData[$data['sku']]['link'] as $linkData) {
                $link = $this->getLinkFactory()->create(['data' => $linkData]);
                if (isset($linkData['type'])) {
                    $link->setLinkType($linkData['type']);
                }
                if (isset($linkData['file'])) {
                    $link->setFile($linkData['file']);
                }
                if (isset($linkData['file_content'])) {
                    $link->setLinkFileContent($linkData['file_content']);
                }
                $link->setId(null);
                if (isset($linkData['sample']['type'])) {
                    $link->setSampleType($linkData['sample']['type']);
                }
                if (isset($linkData['sample']['file'])) {
                    $link->setSampleFileData($linkData['sample']['file']);
                }
                if (isset($linkData['sample']['url'])) {
                    $link->setSampleUrl($linkData['sample']['url']);
                }
                if (isset($linkData['sample']['file_content'])) {
                    $link->setSampleFileContent($linkData['file_content']);
                }
                $link->setStoreId($product->getStoreId());
                $link->setWebsiteId($product->getStore()->getWebsiteId());
                $link->setProductWebsiteIds($product->getWebsiteIds());
                if (!$link->getSortOrder()) {
                    $link->setSortOrder(1);
                }
                if (null === $link->getPrice()) {
                    $link->setPrice(0);
                }
                if ($link->getIsUnlimited()) {
                    $link->setNumberOfDownloads(0);
                }
                $links[] = $link;
            }
            $extension->setDownloadableProductLinks($links);

            $samples = [];
            foreach ($this->downloadableData[$data['sku']]['sample'] as $sampleData) {
                $sample = $this->getSampleFactory()->create(['data' => $sampleData]);
                $sample->setId(null);
                $sample->setStoreId($product->getStoreId());
                if (isset($sampleData['type'])) {
                    $sample->setSampleType($sampleData['type']);
                }
                if (isset($sampleData['file'])) {
                    $sample->setFile($sampleData['file']);
                }
                if (isset($sampleData['sample_url'])) {
                    $sample->setSampleUrl($sampleData['sample_url']);
                }
                if (!$sample->getSortOrder()) {
                    $sample->setSortOrder(1);
                }
                $samples[] = $sample;
            }
            $extension->setDownloadableProductSamples($samples);

            $product->setDownloadableData($this->downloadableData[$data['sku']]);
            $product->setExtensionAttributes($extension);
        }
        $this->setVirtualStockData($product);
        return $this;
    }

    /**
     * Get link interface factory
     *
     * @deprecated
     * @return \Magento\Downloadable\Api\Data\LinkInterfaceFactory
     */
    private function getLinkFactory()
    {
        if (!$this->linkFactory) {
            $this->linkFactory = ObjectManager::getInstance()->get(
                '\Magento\Downloadable\Api\Data\LinkInterfaceFactory'
            );
        }
        return $this->linkFactory;
    }

    /**
     * Get sample interface factory
     *
     * @deprecated
     * @return \Magento\Downloadable\Api\Data\SampleInterfaceFactory
     */
    private function getSampleFactory()
    {
        if (!$this->sampleFactory) {
            $this->sampleFactory = ObjectManager::getInstance()->get(
                '\Magento\Downloadable\Api\Data\SampleInterfaceFactory'
            );
        }
        return $this->sampleFactory;
    }
}
