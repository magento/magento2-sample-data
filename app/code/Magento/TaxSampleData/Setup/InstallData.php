<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TaxSampleData\Setup;

use Magento\Framework\Setup;
use Magento\TaxSampleData\Model\Tax;

/**
 * Class Setup
 * Launches setup of sample data for Tax module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var Tax
     */
    protected $tax;

    /**
     * @param Tax $tax
     */
    public function __construct(
        Tax $tax
    ) {
        $this->tax = $tax;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->tax->install(['Magento_TaxSampleData::fixtures/tax_rate.csv']);
    }
}
