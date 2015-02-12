<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Module\Catalog\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Product links setup
 */
class ProductLink implements SetupInterface
{
    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks
     */
    protected $linksInitializer;

    /**
     * @var \Magento\Tools\SampleData\Helper\PostInstaller
     */
    protected $postInstaller;

    /**
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @param CsvReaderFactory $csvReaderFactory
     * @param FixtureHelper $fixtureHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linksInitializer
     * @param \Magento\Tools\SampleData\Helper\PostInstaller $postInstaller
     * @param \Magento\Tools\SampleData\Logger $logger
     */
    public function __construct(
        CsvReaderFactory $csvReaderFactory,
        FixtureHelper $fixtureHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks $linksInitializer,
        \Magento\Tools\SampleData\Helper\PostInstaller $postInstaller,
        \Magento\Tools\SampleData\Logger $logger
    ) {
        $this->csvReaderFactory = $csvReaderFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->productFactory = $productFactory;
        $this->linksInitializer = $linksInitializer;
        $this->postInstaller = $postInstaller;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing product links:');
        $entityFileAssociation = [
            'related',
            'upsell',
            'crosssell',
        ];

        foreach ($this->postInstaller->getInstalledModuleList() as $moduleName) {
            foreach ($entityFileAssociation as $linkType) {
                $fileName = substr($moduleName, strpos($moduleName, "_") + 1) . '/Links/' . $linkType . '.csv';
                $fileName = $this->fixtureHelper->getPath($fileName);
                if (!$fileName) {
                    continue;
                }
                /** @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory $csvReader */
                $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
                foreach ($csvReader as $row) {
                    /** @var \Magento\Catalog\Model\Product $product */
                    $product = $this->productFactory->create();
                    $productId = $product->getIdBySku($row['sku']);
                    if (!$productId) {
                        continue;
                    }
                    $product->setId($productId);
                    $links = [$linkType => []];
                    foreach (explode("\n", $row['linked_sku']) as $linkedProductSku) {
                        $linkedProductId = $product->getIdBySku($linkedProductSku);
                        if ($linkedProductId) {
                            $links[$linkType][$linkedProductId] = [];
                        }
                    }
                    $this->linksInitializer->initializeLinks($product, $links);
                    $product->getLinkInstance()->saveProductRelations($product);
                    $this->logger->logInline('.');
                }
            }
        }
    }
}
