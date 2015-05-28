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
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param Order\Converter $converter
     * @param Order\Processor $orderProcessor
     * @param \Magento\SampleData\Model\Logger $logger
     * @param array $fixtures
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        Order\Converter $converter,
        Order\Processor $orderProcessor,
        \Magento\SampleData\Model\Logger $logger,
        $fixtures = ['Sales/orders.csv']
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->converter = $converter;
        $this->orderProcessor = $orderProcessor;
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
            foreach ($csvReader as $row) {
                $orderData = $this->converter->convertRow($row);
                $this->orderProcessor->createOrder($orderData);
                $this->logger->logInline('.');
            }
        }
    }
}
