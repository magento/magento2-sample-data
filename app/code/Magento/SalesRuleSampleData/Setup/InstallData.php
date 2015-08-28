<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRuleSampleData\Setup;

use Magento\SalesRuleSampleData\Model;
use Magento\Framework\Setup;

/**
 * Class PostInstallSampleData
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var Model\Rule;
     */
    protected $rule;

    /**
     * @param Model\Rule $rule
     */
    public function __construct(Model\Rule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * @inheritdoc
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->rule->run(['Magento_SalesRuleSampleData::fixtures/sales_rules.csv']);
    }
}
