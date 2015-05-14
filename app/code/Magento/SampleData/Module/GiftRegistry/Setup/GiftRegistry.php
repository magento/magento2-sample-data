<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\GiftRegistry\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\Logger;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class GiftRegistry
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GiftRegistry implements SetupInterface
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
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\GiftRegistry\Model\EntityFactory
     */
    protected $giftRegistryFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\GiftRegistry\Model\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Indexer\Eav\Source
     */
    protected $productIndexer;

    /**
     * @var \Magento\SampleData\Helper\StoreManager
     */
    protected $storeManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\GiftRegistry\Model\EntityFactory $giftRegistryFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\GiftRegistry\Model\ItemFactory $itemFactory
     * @param \Magento\Catalog\Model\Resource\Product\Indexer\Eav\Source $productIndexer
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     * @param Logger $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\GiftRegistry\Model\EntityFactory $giftRegistryFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\GiftRegistry\Model\ItemFactory $itemFactory,
        \Magento\Catalog\Model\Resource\Product\Indexer\Eav\Source $productIndexer,
        \Magento\SampleData\Helper\StoreManager $storeManager,
        Logger $logger
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->countryFactory = $countryFactory;
        $this->giftRegistryFactory = $giftRegistryFactory;
        $this->addressFactory = $addressFactory;
        $this->customerFactory = $customerFactory;
        $this->dateFactory = $dateFactory;
        $this->productFactory = $productFactory;
        $this->itemFactory = $itemFactory;
        $this->productIndexer = $productIndexer;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing Gift Registry:');
        $fixtureFile = 'GiftRegistry/gift_registry.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $giftRegistryData) {
            $data = $this->generateData($giftRegistryData);
            $giftRegistry = $this->giftRegistryFactory->create();
            $address = $this->addressFactory->create();
            $address->setData($data['address']);
            $giftRegistry->setTypeById($data['type_id']);
            $giftRegistry->importData($data);
            $giftRegistry->addData(
                [
                    'customer_id' => $data['customer_id'],
                    'website_id' => $this->storeManager->getWebsiteId(),
                    'url_key' => $giftRegistry->getGenerateKeyId(),
                    'created_at' => $this->dateFactory->create()->date(),
                    'is_add_action' => true,
                ]
            );
            $giftRegistry->importAddress($address);
            $validationPassed = $giftRegistry->validate();
            if ($validationPassed) {
                $giftRegistry->save();
                foreach ($data['items'] as $productId) {
                    $parentId = $this->productIndexer->getRelationsByChild($productId);
                    $itemProduct = $parentId ? $parentId[0] : $productId;
                    $itemOptions = $this->formItemOptions($productId);
                    $item = $this->itemFactory->create();
                    $item->setEntityId($giftRegistry->getId())
                        ->setProductId($itemProduct)
                        ->setQty(1)
                        ->setOptions($itemOptions)
                        ->save();
                }
            }
            $this->logger->logInline('.');
        }
    }

    /**
     * @param array $giftRegistryData
     * @return array
     */
    protected function generateData(array $giftRegistryData)
    {
        $giftRegistryData['sku'] = explode("\n", $giftRegistryData['sku']);
        $customer = $this->customerFactory->create();
        $customer->setWebsiteId($this->storeManager->getWebsiteId())
            ->loadByEmail($giftRegistryData['customer_email']);
        $address = $customer->getDefaultBillingAddress()->getData();
        return [
            'customer_id' => $customer->getId(),
            'type_id' => 1,
            'title' => $giftRegistryData['title'],
            'message' =>  $giftRegistryData['message'],
            'is_public' => 1,
            'is_active' => 1,
            'event_country' => $address['country_id'],
            'event_country_region' => $address['region_id'],
            'event_country_region_text' => '',
            'event_date' => date('Y-m-d'),
            'address' => [
                    'firstname' => $address['firstname'],
                    'lastname' => $address['lastname'],
                    'company' => '',
                    'street' => $address['street'],
                    'city' => $address['city'],
                    'region_id' => $address['region_id'],
                    'region' => $address['region'],
                    'postcode' => $address['postcode'],
                    'country_id' => $address['country_id'],
                    'telephone' => $address['telephone'],
                    'fax' => '',
                ],
            'items' => $this->productSkuToId($giftRegistryData['sku']),
        ];
    }

    /**
     * @param array $skus
     * @return array
     */
    protected function productSkuToId(array $skus)
    {
        $ids = [];
        foreach ($skus as $sku) {
            $id = $this->productFactory->create()->getIdBySku($sku);
            if ($id) {
                $ids[] = $id;
            }
        }
        return $ids;
    }

    /**
     * @param int $productId
     * @return array
     */
    protected function formItemOptions($productId)
    {
        $itemOptions = [];
        $parentId = $this->productIndexer->getRelationsByChild($productId);
        if (!$parentId) {
            $itemOptions[] = [
                'product_id' => $productId,
                'code' => 'info_buyRequest',
                'value' => serialize(['product' => $productId, 'qty' => 1, 'related_product' => '']),
            ];
            return $itemOptions;
        }
        $parentId = array_shift($parentId);
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->productFactory->create();
        $product->load($productId);
        /** @var \Magento\Catalog\Model\Product $parentProduct */
        $parentProduct = $this->productFactory->create();
        $parentProduct->load($parentId);
        $superAttribute = [];
        if ($parentProduct->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $itemOptions[] = [
                'product_id' => $productId,
                'code' => 'simple_product',
                'value' => $productId,
            ];
            $itemOptions[] = [
                'product_id' => $productId,
                'code' => 'product_qty_' . $productId,
                'value' => 1,
            ];
            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productType */
            $productType = $parentProduct->getTypeInstance();
            foreach ($productType->getConfigurableAttributes($parentProduct) as $attribute) {
                $attributeCode = $attribute->getProductAttribute()->getAttributeCode();
                $superAttribute[$attribute->getAttributeId()] = $product->getData($attributeCode);
            }
            $itemOptions[] = [
                'product_id' => $parentId,
                'code' => 'info_buyRequest',
                'value' => serialize([
                    'product' => $productId,
                    'qty' => 1,
                    'related_product' => '',
                    'super_attribute' => $superAttribute,
                ]),
            ];
            $itemOptions[] = [
                'product_id' => $parentId,
                'code' => 'attributes',
                'value' => serialize($superAttribute),
            ];
        }
        return $itemOptions;
    }
}
