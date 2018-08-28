<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ProductLinksSampleData\Model;

use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Product links setup
 */
class ProductLink
{
    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var ProductLinkInterfaceFactory
     */
    private $productLinkFactory;

    /**
     * @var ProductLinkRepositoryInterface
     */
    private $productLinkRepository;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param ProductFactory $productFactory
     * @param ProductLinkInterfaceFactory $productLinkFactory
     * @param ProductLinkRepositoryInterface $productLinkRepository
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        ProductFactory $productFactory,
        ProductLinkInterfaceFactory $productLinkFactory,
        ProductLinkRepositoryInterface $productLinkRepository
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->productFactory = $productFactory;
        $this->productLinkFactory = $productLinkFactory;
        $this->productLinkRepository = $productLinkRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function install(array $related, array $upsell, array $crosssell)
    {
        $linkTypes = [
            'related' => $related,
            'upsell' => $upsell,
            'crosssell' => $crosssell
        ];

        foreach ($linkTypes as $linkType => $fixtures) {
            foreach ($fixtures as $fileName) {
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

                    $product = $this->productFactory->create();
                    $productId = $product->getIdBySku($data['sku']);
                    if (!$productId) {
                        continue;
                    }
                    $product->setId($productId);
                    $product->setSku($data['sku']);
                    $links = $this->productLinkRepository->getList($product);
                    $linkedSkusByType = array_fill_keys(array_keys($linkTypes), []);
                    foreach ($links as $link) {
                        $linkedSkusByType[$link->getLinkType()][] = $link->getLinkedProductSku();
                    }

                    foreach (explode("\n", $data['linked_sku']) as $linkedProductSku) {
                        $linkedProductId = $product->getIdBySku($linkedProductSku);
                        if (!$linkedProductId || in_array($linkedProductSku, $linkedSkusByType[$linkType])) {
                            continue;
                        }

                        $productLink = $this->productLinkFactory->create();
                        $productLink->setSku($data['sku'])
                            ->setLinkedProductSku($linkedProductSku)
                            ->setLinkType($linkType);
                        $links[] = $productLink;
                    }
                    $product->setProductLinks($links);
                    $product->getLinkInstance()->saveProductRelations($product);
                }
            }
        }
    }
}
