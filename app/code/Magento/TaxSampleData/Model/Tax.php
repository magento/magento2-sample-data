<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TaxSampleData\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;

/**
 * Class Tax
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Tax
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
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var \Magento\Framework\Api\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var \Magento\Framework\Setup\SampleData\FixtureManager
     */
    protected $fixtureManager;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * Region collection factory.
     *
     * @var \Magento\Directory\Model\ResourceModel\Region\CollectionFactory
     */
    private $regionCollectionFactory;

    /**
     * @param SampleDataContext $sampleDataContext
     * @param \Magento\Tax\Api\TaxRuleRepositoryInterface $taxRuleRepository
     * @param \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $ruleFactory
     * @param \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository
     * @param \Magento\Tax\Api\Data\TaxRateInterfaceFactory $rateFactory
     * @param \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     */
    public function __construct(
        SampleDataContext $sampleDataContext,
        \Magento\Tax\Api\TaxRuleRepositoryInterface $taxRuleRepository,
        \Magento\Tax\Api\Data\TaxRuleInterfaceFactory $ruleFactory,
        \Magento\Tax\Api\TaxRateRepositoryInterface $taxRateRepository,
        \Magento\Tax\Api\Data\TaxRateInterfaceFactory $rateFactory,
        \Magento\Tax\Model\Calculation\RateFactory $taxRateFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory = null
    ) {
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
        $this->csvReader = $sampleDataContext->getCsvReader();
        $this->taxRuleRepository = $taxRuleRepository;
        $this->ruleFactory = $ruleFactory;
        $this->taxRateRepository = $taxRateRepository;
        $this->rateFactory = $rateFactory;
        $this->taxRateFactory = $taxRateFactory;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->regionCollectionFactory = $regionCollectionFactory ?: ObjectManager::getInstance()->get(
            \Magento\Directory\Model\ResourceModel\Region\CollectionFactory::class
        );
    }

    /**
     * {@inheritdoc}
     */
    public function install(array $fixtures)
    {
        $this->createTaxRates($fixtures);
        $this->createTaxRules();
    }

    /**
     * Create tax rates.
     *
     * @param array $fixtures
     * @return void
     * @throws \Exception if something went wrong while saving the tax rate.
     */
    private function createTaxRates(array $fixtures)
    {
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
                if ($this->rateFactory->create()->loadByCode($data['code'])->getId()) {
                    continue;
                }
                $taxRate = $this->rateFactory->create();
                $regionId = $this->getRegionId($data['tax_region_name'], $data['tax_country_id']);
                $taxRate->setCode($data['code'])
                    ->setTaxCountryId($data['tax_country_id'])
                    ->setTaxRegionId($regionId)
                    ->setTaxPostcode($data['tax_postcode'])
                    ->setRate($data['rate']);
                $this->taxRateRepository->save($taxRate);
            }
        }
    }

    /**
     * Create tax rules.
     *
     * @return void
     * @throws \Exception if something went wrong while saving the tax rule.
     */
    private function createTaxRules()
    {
        $fixtureFile = 'Magento_TaxSampleData::fixtures/tax_rule.csv';
        $fixtureFileName = $this->fixtureManager->getFixture($fixtureFile);
        if (file_exists($fixtureFileName)) {

            $rows = $this->csvReader->getData($fixtureFileName);
            $header = array_shift($rows);

            foreach ($rows as $row) {
                $data = [];
                foreach ($row as $key => $value) {
                    $data[$header[$key]] = $value;
                }
                $filter = $this->filterBuilder->setField('code')
                    ->setConditionType('=')
                    ->setValue($data['code'])
                    ->create();
                $criteria = $this->criteriaBuilder->addFilters([$filter])->create();
                $existingRates = $this->taxRuleRepository->getList($criteria)->getItems();
                if (!empty($existingRates)) {
                    continue;
                }

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
            }
        }
    }

    /**
     * Return region Id by code or name.
     *
     * @param string $region
     * @param string $countryId
     * @return string|null
     */
    private function getRegionId($region, $countryId)
    {
        $regionCollection = $this->regionCollectionFactory->create();
        $regionCollection->addCountryFilter($countryId)
            ->addRegionCodeOrNameFilter($region)
            ->setPageSize(1);

        return $regionCollection->getFirstItem()->getId();
    }
}
