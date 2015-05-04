<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Module\SalesRule\Setup;

use Magento\SalesRule\Model\RuleFactory as RuleFactory;
use Magento\SampleData\Helper\Csv\ReaderFactory as CsvReaderFactory;
use Magento\SampleData\Helper\Fixture as FixtureHelper;
use Magento\SampleData\Model\Logger;
use Magento\SampleData\Module\CatalogRule\Setup\Rule as CatalogRule;
use Magento\SampleData\Model\SetupInterface;

/**
 * Class Rule
 */
class Rule implements SetupInterface
{
    /**
     * @var CsvReaderFactory
     */
    protected $csvReaderFactory;

    /**
     * @var \Magento\SampleData\Helper\Fixture
     */
    protected $fixtureHelper;

    /**
     * @var RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var CatalogRule
     */
    protected $catalogRule;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @param CsvReaderFactory $csvReaderFactory
     * @param FixtureHelper $fixtureHelper
     * @param RuleFactory $ruleFactory
     * @param CatalogRule $catalogRule
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param Logger $logger
     */
    public function __construct(
        CsvReaderFactory $csvReaderFactory,
        FixtureHelper $fixtureHelper,
        RuleFactory $ruleFactory,
        CatalogRule $catalogRule,
        \Magento\Eav\Model\Config $eavConfig,
        Logger $logger
    ) {
        $this->csvReaderFactory = $csvReaderFactory;
        $this->fixtureHelper = $fixtureHelper;
        $this->ruleFactory = $ruleFactory;
        $this->catalogRule = $catalogRule;
        $this->eavConfig = $eavConfig;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->logger->log('Installing sales rules:');
        $file = 'SalesRule/sales_rules.csv';
        $fileName = $this->fixtureHelper->getPath($file);
        $csvReader = $this->csvReaderFactory->create(['fileName' => $fileName, 'mode' => 'r']);
        $attribute = $this->eavConfig->getAttribute('catalog_product', 'sku');
        if ($attribute->getIsUsedForPromoRules() == 0) {
            $attribute->setIsUsedForPromoRules('1')->save();
        }
        foreach ($csvReader as $row) {
            $row['customer_group_ids'] = $this->catalogRule->getGroupIds();
            $row['website_ids'] = $this->catalogRule->getWebsiteIds();
            $row['conditions_serialized'] = $this->catalogRule->convertSerializedData($row['conditions_serialized']);
            $row['actions_serialized'] = $this->catalogRule->convertSerializedData($row['actions_serialized']);
            $rule = $this->ruleFactory->create();
            $rule->loadPost($row);
            $rule->save();
            $this->logger->logInline('.');
        }
    }
}
