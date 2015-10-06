<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TargetRuleSampleData\Setup;

use Magento\TargetRuleSampleData\Model\Rule;
use Magento\Framework\Setup;

/**
 * Class Setup
 * Launches setup of sample data for TargetRule module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * Model class for products
     *
     * @var Rule
     */
    protected $rule;

    /**
     * Constructor
     *
     * @param Rule $rule
     */
    public function __construct(
        Rule $rule

    ) {
        $this->rule = $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
//        $this->removeSetupResourceType('Magento\CatalogSampleData\Setup\ProductLink');
        $this->rule->install(
            [
                \Magento\TargetRule\Model\Rule::RELATED_PRODUCTS => 'Magento_TargetRuleSampleData::fixtures/crossell.csv',
                \Magento\TargetRule\Model\Rule::UP_SELLS => 'Magento_TargetRuleSampleData::fixtures/related.csv',
                \Magento\TargetRule\Model\Rule::CROSS_SELLS => 'Magento_TargetRuleSampleData::fixtures/upsell.csv'
            ]
        );
    }
}
