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
 * Setup sample attributes
 *
 * Class Attribute
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Attribute implements SetupInterface
{
    /**
     * @var \Magento\Catalog\Model\Resource\Eav\AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $attributeSetFactory;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory
     */
    protected $attrOptionCollectionFactory;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var FixtureHelper
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /*
     * @var int
     */
    protected $entityTypeId;

    /**
     * @var \Magento\SampleData\Model\Logger
     */
    protected $logger;

    /**
     * @var \Magento\SampleData\Helper\StoreManager
     */
    protected $storeManager;

    /**
     * @param \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param FixtureHelper $fixtureHelper
     * @param \Magento\SampleData\Helper\StoreManager $storeManager,
     * @param \Magento\SampleData\Model\Logger $logger
     * @param CsvReaderFactory $csvReaderFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Catalog\Model\Resource\Eav\AttributeFactory $attributeFactory,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        FixtureHelper $fixtureHelper,
        \Magento\SampleData\Helper\StoreManager $storeManager,
        \Magento\SampleData\Model\Logger $logger,
        CsvReaderFactory $csvReaderFactory
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
        $this->productHelper = $productHelper;
        $this->eavConfig = $eavConfig;
        $this->moduleList = $moduleList;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing catalog attributes:');
        $attributeCount = 0;

        foreach ($this->moduleList->getNames() as $moduleName) {
            $fileName = substr($moduleName, strpos($moduleName, "_") + 1) . '/attributes.csv';
            $fileName = $this->fixtureHelper->getPath($fileName);
            if (!$fileName) {
                continue;
            }
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            foreach ($csvReader as $data) {
                $data['attribute_set'] = explode("\n", $data['attribute_set']);

                /** @var \Magento\Catalog\Model\Resource\Eav\Attribute $attribute */
                $attribute = $this->eavConfig->getAttribute('catalog_product', $data['attribute_code']);
                if (!$attribute) {
                    $attribute = $this->attributeFactory->create();
                }

                $frontendLabel = explode("\n", $data['frontend_label']);
                if (count($frontendLabel) > 1) {
                    $data['frontend_label'] = [];
                    $data['frontend_label'][\Magento\Store\Model\Store::DEFAULT_STORE_ID] = $frontendLabel[0];
                    $data['frontend_label'][$this->storeManager->getStoreId()] = $frontendLabel[1];
                }
                $data['option'] = $this->getOption($attribute, $data);
                $data['source_model'] = $this->productHelper->getAttributeSourceModelByInputType(
                    $data['frontend_input']
                );
                $data['backend_model'] = $this->productHelper->getAttributeBackendModelByInputType(
                    $data['frontend_input']
                );
                $data += ['is_filterable' => 0, 'is_filterable_in_search' => 0, 'apply_to' => []];
                $data['backend_type'] = $attribute->getBackendTypeByInput($data['frontend_input']);

                $attribute->addData($data);
                $attribute->setIsUserDefined(1);

                $attribute->setEntityTypeId($this->getEntityTypeId());
                $attribute->save();
                $attributeId = $attribute->getId();

                if (is_array($data['attribute_set'])) {
                    foreach ($data['attribute_set'] as $setName) {
                        $setName = trim($setName);
                        $attributeCount++;
                        $attributeSet = $this->processAttributeSet($setName);
                        $attributeGroupId = $attributeSet->getDefaultGroupId();

                        $attribute = $this->attributeFactory->create();
                        $attribute
                            ->setId($attributeId)
                            ->setAttributeGroupId($attributeGroupId)
                            ->setAttributeSetId($attributeSet->getId())
                            ->setEntityTypeId($this->getEntityTypeId())
                            ->setSortOrder($attributeCount + 999)
                            ->save();
                    }
                }

                $this->logger->logInline('.');
            }
        }

        $this->eavConfig->clear();
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Eav\Attribute $attribute
     * @param array $data
     * @return array
     */
    protected function getOption($attribute, $data)
    {
        $result = [];
        $data['option'] = explode("\n", $data['option']);
        /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection $options */
        $options = $this->attrOptionCollectionFactory->create()
            ->setAttributeFilter($attribute->getId())
            ->setPositionOrder('asc', true)
            ->load();
        foreach ($data['option'] as $value) {
            if (!$options->getItemByColumnValue('value', $value)) {
                $result[] = $value;
            }
        }
        return $result ? $this->convertOption($result) : $result;
    }

    /**
     * Converting attribute options from csv to correct sql values
     *
     * @param array $values
     * @return array
     */
    protected function convertOption($values)
    {
        $result = ['order' => [], 'value' => []];
        $i = 0;
        foreach ($values as $value) {
            $result['order']['option_' . $i] = (string)$i;
            $result['value']['option_' . $i] = [0 => $value, 1 => ''];
            $i++;
        }
        return $result;
    }

    /**
     * @return int
     * @throws \Magento\Framework\Model\Exception
     */
    protected function getEntityTypeId()
    {
        if (!$this->entityTypeId) {
            $this->entityTypeId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
        }
        return $this->entityTypeId;
    }

    /**
     * Loads attribute set by name if attribute with such name exists
     * Otherwise creates the attribute set with $setName name and return it
     *
     * @param string $setName
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     * @throws \Exception
     * @throws \Magento\Framework\Model\Exception
     */
    protected function processAttributeSet($setName)
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $setCollection = $attributeSet->getResourceCollection()
            ->addFieldToFilter('entity_type_id', $this->getEntityTypeId())
            ->addFieldToFilter('attribute_set_name', $setName)
            ->load();
        $attributeSet = $setCollection->fetchItem();

        if (!$attributeSet) {
            $attributeSet = $this->attributeSetFactory->create();
            $attributeSet->setEntityTypeId($this->getEntityTypeId());
            $attributeSet->setAttributeSetName($setName);
            $attributeSet->save();
            $defaultSetId = $this->eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
                ->getDefaultAttributeSetId();
            $attributeSet->initFromSkeleton($defaultSetId);
            $attributeSet->save();
        }
        return $attributeSet;
    }
}
