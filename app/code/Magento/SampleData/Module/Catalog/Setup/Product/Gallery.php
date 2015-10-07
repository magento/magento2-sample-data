<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Catalog\Setup\Product;

use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Media as GalleryAttribute;
use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\Logger;

/**
 * Class Gallery
 */
class Gallery
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
     * @var array
     */
    protected $images;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var array
     */
    protected $fixtures = [
        'Catalog/SimpleProduct/images_gear_bags.csv',
        'Catalog/SimpleProduct/images_gear_fitness_equipment.csv',
        'Catalog/SimpleProduct/images_gear_watches.csv',
    ];

    /**
     * @var false|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected $mediaAttribute;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $resource;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param ProductFactory $productFactory
     * @param GalleryAttribute $galleryAttribute
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\Resource $resource
     * @param Logger $logger
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        ProductFactory $productFactory,
        GalleryAttribute $galleryAttribute,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\Resource $resource,
        Logger $logger
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->galleryAttribute = $galleryAttribute;
        $this->productFactory = $productFactory;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->mediaAttribute = $eavConfig->getAttribute('catalog_product', 'media_gallery');
        $this->resource = $resource;
        $this->logger = $logger;
        $this->loadFixtures();
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function install($product)
    {
        if (!empty($this->images[$product->getSku()])) {
            $this->storeImage($product, $this->images[$product->getSku()]);
        } else {
            $this->errors[] = $product->getSku();
        }
    }

    /**
     * Save image information to DB.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $images
     * @return void
     */
    protected function storeImage($product, $images)
    {
        $baseImage = '';
        $i = 1;
        foreach ($images as $image) {
            if (empty($image)) {
                $this->errors[] = $product->getSku();
                continue;
            }
            if (strpos($image, '_main') !== false) {
                $baseImage = $image;
            }
            $id = $this->galleryAttribute->insertGallery([
                'attribute_id' => $this->mediaAttribute->getAttributeId(),
                'entity_id' => $product->getId(),
                'value' => $image,
            ]);
            $this->galleryAttribute->insertGalleryValueInStore([
                'value_id' => $id,
                'store_id' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                'entity_id' => $product->getId(),
                'label' => 'Image',
                'position' => $i,
                'disables' => 0,
            ]);
            $i++;
        }

        if (empty($baseImage)) {
            $baseImage = $images[0];
        }

        if ($baseImage) {
            $imageAttribute = $product->getResource()->getAttribute('image');
            $smallImageAttribute = $product->getResource()->getAttribute('small_image');
            $thumbnailAttribute = $product->getResource()->getAttribute('thumbnail');
            $adapter = $this->resource->getConnection('core');
            foreach ([$imageAttribute, $smallImageAttribute, $thumbnailAttribute] as $attribute) {
                $table = $imageAttribute->getBackend()->getTable();
                /** @var \Magento\Framework\DB\Adapter\AdapterInterface $adapter*/
                $data = [
                    $attribute->getBackend()->getEntityIdField() => $product->getId(),
                    'attribute_id' => $attribute->getId(),
                    'value' => $baseImage,
                ];
                $adapter->insertOnDuplicate($table, $data, ['value']);
            }
        }
    }

    /**
     * Set fixtures
     * @param array $fixtures
     * @return void
     */
    public function setFixtures($fixtures)
    {
        $this->fixtures = $fixtures;
        $this->loadFixtures();
    }

    /**
     * Load data from fixtures
     * @return void
     */
    protected function loadFixtures()
    {
        $this->images = [];
        foreach ($this->fixtures as $file) {
            /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            foreach ($csvReader as $row) {
                $this->images[$row['sku']][] = $row['image'];
            }
        }
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        if (!empty($this->errors)) {
            $this->logger->log('No images found for: ' . PHP_EOL . implode(',', $this->errors));
        }
    }
}
