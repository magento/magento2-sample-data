<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesRuleSampleData\Setup;

use Magento\SalesRuleSampleData\Model;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\PostInstallSampleDataInterface;

/**
 * Class PostInstallSampleData
 */
class PostInstallSampleData implements PostInstallSampleDataInterface
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
     * Installs optional data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $moduleContext
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $moduleContext)
    {
        $this->rule->run(['Magento_SalesRuleSampleData::fixtures/sales_rules.csv']);
    }
}
