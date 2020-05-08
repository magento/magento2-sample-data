<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TaxSampleData\Test\Model;

use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Framework\File\Csv;
use Magento\Framework\Setup\SampleData\FixtureManager;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Tax\Api\Data\TaxRateInterface;
use Magento\Tax\Api\Data\TaxRateInterfaceFactory;
use Magento\Tax\Api\Data\TaxRuleInterface;
use Magento\Tax\Api\Data\TaxRuleInterfaceFactory;
use Magento\Tax\Api\Data\TaxRuleSearchResultsInterface;
use Magento\Tax\Api\TaxRateRepositoryInterface;
use Magento\Tax\Api\TaxRuleRepositoryInterface;
use Magento\Tax\Model\Calculation\RateFactory;
use Magento\TaxSampleData\Model\Tax;

/**
 * Magento\TaxSampleData\Model\Tax test.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TaxTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Tax
     */
    private $taxModel;

    /**
     * @var TaxRuleRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $taxRuleRepository;

    /**
     * @var TaxRuleInterfaceFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $ruleFactory;

    /**
     * @var TaxRateRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $taxRateRepository;

    /**
     * @var TaxRateInterfaceFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $rateFactory;

    /**
     * @var RateFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $taxRateFactory;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $criteriaBuilder;

    /**
     * @var FilterBuilder|\PHPUnit\Framework\MockObject\MockObject
     */
    private $filterBuilder;

    /**
     * @var FixtureManager|\PHPUnit\Framework\MockObject\MockObject
     */
    private $fixtureManager;

    /**
     * @var Csv|\PHPUnit\Framework\MockObject\MockObject
     */
    private $csvReader;

    /**
     * Region collection factory.
     *
     * @var CollectionFactory|\PHPUnit\Framework\MockObject\MockObject
     */
    private $regionCollectionFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->taxRuleRepository = $this->getMockBuilder(TaxRuleRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMockForAbstractClass();
        $this->ruleFactory = $this->getMockBuilder(TaxRuleInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMockForAbstractClass();
        $this->taxRateRepository = $this->getMockBuilder(TaxRateRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['save'])
            ->getMockForAbstractClass();
        $this->taxRateRepository->expects(self::any())
            ->method('save')
            ->willReturnSelf();
        $this->rateFactory = $this->getMockBuilder(TaxRateInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMockForAbstractClass();
        $this->taxRateFactory = $this->getMockBuilder(RateFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMockForAbstractClass();
        $this->criteriaBuilder = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['addFilters', 'create'])
            ->getMock();
        $this->filterBuilder = $this->getMockBuilder(FilterBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['setField', 'setConditionType', 'setValue', 'create'])
            ->getMock();
        $this->regionCollectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMockForAbstractClass();

        $this->taxModel = $this->objectManager->getObject(
            Tax::class,
            [
                'sampleDataContext' => $this->prepareContext(),
                'taxRuleRepository' => $this->taxRuleRepository,
                'ruleFactory' => $this->ruleFactory,
                'taxRateRepository' => $this->taxRateRepository,
                'rateFactory' => $this->rateFactory,
                'taxRateFactory' => $this->taxRateFactory,
                'criteriaBuilder' => $this->criteriaBuilder,
                'filterBuilder' => $this->filterBuilder,
                'regionCollectionFactory' => $this->regionCollectionFactory
            ]
        );
    }

    /**
     * @dataProvider installDataProvider
     *
     * @param array $fixtures
     * @return void
     */
    public function testInstall(array $fixtures)
    {
        $this->prepareContext();

        $taxRate = $this->prepareTaxRate();

        $this->rateFactory->expects(self::exactly(2))
            ->method('create')
            ->willReturn($taxRate);

        $regionCollection = $this->prepareRegionCollection();

        $this->regionCollectionFactory->expects(self::atLeastOnce())
            ->method('create')
            ->willReturn($regionCollection);

        $this->prepareFilters();

        $this->taxRateFactory->expects(self::atLeastOnce())
            ->method('create')
            ->willReturn($taxRate);

        $taxRule = $this->prepareTaxRule();

        $this->taxRuleRepository->expects(self::once())
            ->method('save')
            ->with($taxRule)
            ->willReturn($taxRule);

        $this->taxModel->install($fixtures);
    }

    /**
     * @return array
     */
    public function installDataProvider()
    {
        return [
            [
                ['Magento_TaxSampleData::fixtures/tax_rate.csv'],
            ]
        ];
    }

    /**
     * Prepare region collection mock.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function prepareRegionCollection()
    {
        $regionCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'addCountryFilter',
                    'addRegionCodeOrNameFilter',
                    'setPageSize',
                    'getFirstItem',
                ]
            )
            ->getMock();

        $regionCollection->expects(self::any())
            ->method('addCountryFilter')
            ->willReturnSelf();
        $regionCollection->expects(self::any())
            ->method('addRegionCodeOrNameFilter')
            ->willReturnSelf();
        $regionCollection->expects(self::any())
            ->method('setPageSize')
            ->willReturnSelf();
        $regionCollection->expects(self::any())
            ->method('setPageSize')
            ->willReturnSelf();
        $regionCollection->expects(self::any())
            ->method('getFirstItem')
            ->willReturn(new DataObject(['id' => 1]));

        return $regionCollection;
    }

    /**
     * Prepare context object.
     *
     * @return \Magento\Framework\Setup\SampleData\Context
     */
    private function prepareContext()
    {
        $ratesFile = __DIR__ . '/../../fixtures/tax_rate.csv';
        $ruleFile = __DIR__ . '/../../fixtures/tax_rule.csv';

        $this->fixtureManager = $this->getMockBuilder(FixtureManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFixture'])
            ->getMock();

        $this->csvReader = $this->getMockBuilder(Csv::class)
            ->disableOriginalConstructor()
            ->setMethods(['getData'])
            ->getMock();

        $this->fixtureManager->expects(self::any())
            ->method('getFixture')
            ->willReturnMap(
                [
                    ['Magento_TaxSampleData::fixtures/tax_rate.csv', $ratesFile],
                    ['Magento_TaxSampleData::fixtures/tax_rule.csv', $ruleFile],
                ]
            );

        $this->csvReader->expects(self::any())
            ->method('getData')
            ->willReturnMap(
                [
                    [
                        $ratesFile,
                        [
                            ['code', 'tax_country_id', 'tax_region_name', 'tax_postcode', 'rate'],
                            ['US-MI-*-Rate 1', 'US', 'Michigan', '*', 8.25],
                        ],
                    ],
                    [
                        $ruleFile,
                        [
                            [
                                'code',
                                'tax_rate',
                                'tax_customer_class',
                                'tax_product_class',
                                'priority',
                                'calculate_subtotal',
                                'position',
                            ],
                            ['Rule1', 'US-MI-*-Rate 1', 3, 2, 0, '', 0],
                        ],
                    ],
                ]
            );

        $sampleDataContext = $this->objectManager->getObject(
            \Magento\Framework\Setup\SampleData\Context::class,
            [
                'fixtureManager' => $this->fixtureManager,
                'csvReader' => $this->csvReader,
            ]
        );

        return $sampleDataContext;
    }

    /**
     * Prepare tax rate mock.
     *
     * @return TaxRateInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function prepareTaxRate()
    {
        $taxRate = $this->getMockBuilder(TaxRateInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['loadByCode'])
            ->getMockForAbstractClass();

        $taxRate->expects(self::exactly(2))
            ->method('loadByCode')
            ->willReturnSelf();
        $taxRate->expects(self::exactly(2))
            ->method('getId')
            ->willReturn('');
        $taxRate->expects(self::once())
            ->method('setCode')
            ->willReturnSelf();
        $taxRate->expects(self::once())
            ->method('setTaxCountryId')
            ->willReturnSelf();
        $taxRate->expects(self::once())
            ->method('setTaxRegionId')
            ->willReturnSelf();
        $taxRate->expects(self::once())
            ->method('setTaxPostcode')
            ->willReturnSelf();
        $taxRate->expects(self::once())
            ->method('setRate')
            ->willReturnSelf();

        return $taxRate;
    }

    /**
     * Prepare filter objects.
     *
     * @return void
     */
    private function prepareFilters()
    {
        $this->filterBuilder->expects(self::once())
            ->method('setField')
            ->willReturnSelf();
        $this->filterBuilder->expects(self::once())
            ->method('setConditionType')
            ->willReturnSelf();
        $this->filterBuilder->expects(self::once())
            ->method('setValue')
            ->willReturnSelf();
        $this->filterBuilder->expects(self::once())
            ->method('create')
            ->willReturn(new DataObject([]));

        $this->criteriaBuilder->expects(self::once())
            ->method('addFilters')
            ->willReturnSelf();

        $searchCriteria = $this->createMock(SearchCriteria::class);

        $this->criteriaBuilder->expects(self::once())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResult = $this->getMockBuilder(TaxRuleSearchResultsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getItems'])
            ->getMockForAbstractClass();
        $searchResult->expects(self::once())
            ->method('getItems')
            ->willReturn([]);

        $this->taxRuleRepository->expects(self::once())
            ->method('getList')
            ->willReturn($searchResult);
    }

    /**
     * Prepare tax rule mock.
     *
     * @return TaxRuleInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function prepareTaxRule()
    {
        $taxRule = $this->getMockBuilder(TaxRuleInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(
                [
                    'setCode',
                    'setTaxRateIds',
                    'setCustomerTaxClassIds',
                    'setProductTaxClassIds',
                    'setPriority',
                    'setCalculateSubtotal',
                    'setPosition',
                ]
            )
            ->getMockForAbstractClass();

        $taxRule->expects(self::once())
            ->method('setCode')
            ->willReturnSelf();
        $taxRule->expects(self::once())
            ->method('setTaxRateIds')
            ->willReturnSelf();
        $taxRule->expects(self::once())
            ->method('setCustomerTaxClassIds')
            ->willReturnSelf();
        $taxRule->expects(self::once())
            ->method('setProductTaxClassIds')
            ->willReturnSelf();
        $taxRule->expects(self::once())
            ->method('setPriority')
            ->willReturnSelf();
        $taxRule->expects(self::once())
            ->method('setCalculateSubtotal')
            ->willReturnSelf();
        $taxRule->expects(self::once())
            ->method('setPosition')
            ->willReturnSelf();

        $this->ruleFactory->expects(self::once())
            ->method('create')
            ->willReturn($taxRule);

        return $taxRule;
    }
}
