<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\Sales\Setup;

use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Order
 */
class Order implements SetupInterface
{
    /**
     * @var \Magento\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\SampleData\Module\Sales\Setup\Order\Converter
     */
    protected $converter;

    /**
     * @var \Magento\SampleData\Module\Sales\Setup\Order\Processor
     */
    protected $orderProcessor;

    /**
     * @var \Magento\SampleData\Model\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Order\Converter $converter
     * @param Order\Processor $orderProcessor
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\SampleData\Model\Logger $logger
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Order\Converter $converter,
        Order\Processor $orderProcessor,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\SampleData\Model\Logger $logger,
        $fixtures = ['Sales/orders.csv']
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->converter = $converter;
        $this->orderProcessor = $orderProcessor;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->customerRepository = $customerRepository;
        $this->fixtures = $fixtures;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing orders:');
        foreach ($this->fixtures as $file) {
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            $isFirst = true;
            foreach ($csvReader as $row) {
                if ($isFirst) {
                    $customer = $this->customerRepository->get($row['customer_email']);
                    if (!$customer->getId()) {
                        continue;
                    }
                    /** @var \Magento\Sales\Model\ResourceModel\Collection $orderCollection */
                    $orderCollection = $this->orderCollectionFactory->create();
                    $orderCollection->addFilter('customer_id', $customer->getId());
                    if ($orderCollection->count() > 0) {
                        break;
                    }
                }
                $isFirst = false;
                $orderData = $this->converter->convertRow($row);
                $this->orderProcessor->createOrder($orderData);
                $this->logger->logInline('.');
            }
        }
    }
}
