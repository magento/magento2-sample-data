<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData\Module\Cms\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Launches setup of sample data for CMS Page
 */
class Page implements SetupInterface
{
    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $fixtures;

    /**
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Tools\SampleData\Logger $logger
     * @param array $fixtures
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Tools\SampleData\Logger $logger,
        $fixtures = [
            'Cms/Page/pages.csv',
        ]
    ) {
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->pageFactory = $pageFactory;
        $this->fixtures = $fixtures;
        $this->logger = $logger;
    }
    // @codingStandardsIgnoreEnd

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing CMS pages:');

        foreach ($this->fixtures as $file) {
            /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
            $fileName = $this->fixtureHelper->getPath($file);
            $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
            foreach ($csvReader as $row) {
                $this->pageFactory->create()
                    ->load($row['identifier'], 'identifier')
                    ->addData($row)
                    ->setStores([\Magento\Store\Model\Store::DEFAULT_STORE_ID])
                    ->save();
                $this->logger->logInline('.');
            }
        }
    }
}
