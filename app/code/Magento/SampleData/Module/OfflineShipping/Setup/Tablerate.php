<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\OfflineShipping\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\Logger;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Tablerate
 */
class Tablerate implements SetupInterface
{
    /**
     * Code of "Integrity constraint violation: 1062 Duplicate entry" error
     */
    const ERROR_CODE_DUPLICATE_ENTRY = 23000;

    /**
     * @var \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate
     */
    protected $tablerate;

    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate $tablerate
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @var \Magento\SampleData\Helper\StoreManager
     */
    protected $storeManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate $tablerate
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\SampleData\Helper\StoreManager $storeManager
     * @param Logger $logger
     */
    public function __construct(
        \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate $tablerate,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\SampleData\Helper\StoreManager $storeManager,
        Logger $logger
    ) {
        $this->tablerate = $tablerate;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->resource = $resource;
        $this->regionCollectionFactory = $regionCollectionFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->configWriter = $configWriter;
        $this->storeManager = $storeManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing Tablerate:');
        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->resource->getConnection('core');
        $fixtureFile = 'OfflineShipping/tablerate.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        $regions = $this->loadDirectoryRegions();
        /** @var \Magento\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $data) {
            $regionId = ($data['region'] != '*')
                ? $regions[$data['country']][$data['region']]
                : 0;
            try {
                $connection->insert(
                    $this->tablerate->getMainTable(),
                    [
                        'website_id' => $this->storeManager->getWebsiteId(),
                        'dest_country_id' => $data['country'],
                        'dest_region_id' => $regionId,
                        'dest_zip' => $data['zip'],
                        'condition_name' => 'package_value',
                        'condition_value' => $data['order_subtotal'],
                        'price' => $data['price'],
                        'cost' => 0,
                    ]
                );
            } catch (\Zend_Db_Statement_Exception $e) {
                if ($e->getCode() == self::ERROR_CODE_DUPLICATE_ENTRY) {
                    // In case if Sample data was already installed we just skip duplicated records installation
                    continue;
                } else {
                    throw $e;
                }
            }
            $this->logger->logInline('.');
        }
        $this->configWriter->save('carriers/tablerate/active', 1);
        $this->configWriter->save('carriers/tablerate/condition_name', 'package_value');
        $this->cacheTypeList->cleanType('config');
    }

    /**
     * Load directory regions
     *
     * @return array
     */
    protected function loadDirectoryRegions()
    {
        $importRegions = [];
        /** @var $collection \Magento\Directory\Model\ResourceModel\Region\Collection */
        $collection = $this->regionCollectionFactory->create();
        foreach ($collection->getData() as $row) {
            $importRegions[$row['country_id']][$row['code']] = (int)$row['region_id'];
        }
        return $importRegions;
    }
}
