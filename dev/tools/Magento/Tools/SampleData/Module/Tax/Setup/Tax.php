<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Module\Tax\Setup;

use Magento\Tools\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\Tools\SampleData\Helper\Fixture as FixtureHelper;
use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Tax
 */
class Tax implements SetupInterface
{
    /**
     * @var \Magento\Tax\Api\TaxRuleRepositoryInterface
     */
    protected $taxRuleRepository;

    /**
     * @var \Magento\Tax\Api\Data\TaxRuleInterfaceFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Tax\Api\TaxRateRepositoryInterface
     */
    protected $taxRateRepository;

    /**
     * @var \Magento\Tax\Api\Data\TaxRateInterfaceFactory
     */
    protected $rateFactory;

    /**
     * @var \Magento\Tax\Model\Calculation\RateFactory
     */
    protected $taxRateFactory;

    /**
     * @var \Magento\Tools\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var \Magento\Tools\SampleData\Helper\Csv\ReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\Tools\SampleData\Logger
     */
    protected $logger;

    /**
     * @param \Magento\Tax\Api\TaxRuleRepositoryInterface $taxRuleRepository
     * @param \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $ruleFactory
     * @param \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository
     * @param \Magento\Tax\Api\Data\TaxRateInterfaceFactory $rateFactory
     * @param \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory
     * @param FixtureHelper $fixtureHelper
     * @param CsvReaderFactory $csvReaderFactory
     * @param \Magento\Tools\SampleData\Logger $logger
     */
    public function __construct(
        \Magento\Tax\Api\TaxRuleRepositoryInterface $taxRuleRepository,
        \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $ruleFactory,
        \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository,
        \Magento\Tax\Api\Data\TaxRateInterfaceFactory $rateFactory,
        \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory,
        FixtureHelper $fixtureHelper,
        CsvReaderFactory $csvReaderFactory,
        \Magento\Tools\SampleData\Logger $logger
    ) {
        $this->taxRuleRepository = $taxRuleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->rateFactory = $rateFactory;
        $this->taxRateFactory = $taxRateFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->csvReaderFactory = $csvReaderFactory;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing taxes:');
        $fixtureFile = 'Tax/tax_rate.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $data) {
            $taxRate = $this->rateFactory->create();
            $taxRate->setCode($data['code'])
                ->setTaxCountryId($data['tax_country_id'])
                ->setTaxRegionId($data['tax_region_id'])
                ->setTaxPostcode($data['tax_postcode'])
                ->setRate($data['rate']);
            $this->taxRateRepository->save($taxRate);
            $this->logger->logInline('.');
        }

        $fixtureFile = 'Tax/tax_rule.csv';
        $fixtureFilePath = $this->fixtureHelper->getPath($fixtureFile);
        /** @var \Magento\Tools\SampleData\Helper\Csv\Reader $csvReader */
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fixtureFilePath, 'mode' => 'r']);
        foreach ($csvReader as $data) {
            $taxRate = $this->taxRateFactory->create()->loadByCode($data['tax_rate']);
            $taxRule = $this->ruleFactory->create();
            $taxRule->setCode($data['code'])
                ->setTaxRateIds([$taxRate->getId()])
                ->setCustomerTaxClassIds([$data['tax_customer_class']])
                ->setProductTaxClassIds([$data['tax_product_class']])
                ->setPriority($data['priority'])
                ->setCalculateSubtotal($data['calculate_subtotal'])
                ->setPosition($data['position']);
            $this->taxRuleRepository->save($taxRule);
            $this->logger->logInline('.');
        }
    }
}
