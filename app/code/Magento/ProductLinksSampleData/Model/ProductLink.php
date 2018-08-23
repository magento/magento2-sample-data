<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\ProductLinksSampleData\Model;

use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException;
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
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductLinkRepositoryInterface
     */
    private $productLinkRepository;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param ProductFactory $productFactory
     * @param ProductRepository $productRepository
     * @param ProductLinkRepositoryInterface $productLinkRepository
     * @param ProductLinkInterfaceFactory $productLinkFactory
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        ProductFactory $productFactory,
        ProductRepository $productRepository,
        ProductLinkRepositoryInterface $productLinkRepository,
        ProductLinkInterfaceFactory $productLinkFactory
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->productFactory = $productFactory;
        $this->productRepository = $productRepository;
        $this->productLinkRepository = $productLinkRepository;
        $this->productLinkFactory = $productLinkFactory;
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

                    try {
                        $product = $this->productRepository->get($data['sku']);
                    } catch (NoSuchEntityException $e) {
                        continue;
                    }

                    $linkedProductSkus = explode("\n", $data['linked_sku']);
                    foreach ($linkedProductSkus as $linkedProductSku) {
                        $productLink = $this->productLinkFactory->create();
                        $productLink->setSku($product->getSku())
                            ->setLinkedProductSku($linkedProductSku)
                            ->setLinkType($linkType);
                        try {
                            $this->productLinkRepository->save($productLink);
                        } catch (NoSuchEntityException $e) {
                            continue;
                        }
                    }
                }
            }
        }
    }
}
