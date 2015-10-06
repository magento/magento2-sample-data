<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSampleData\Setup;

use Magento\Framework\Setup;

/**
 * Class Setup
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * Setup class for customer
     *
     * @var \Magento\CustomerSampleData\Model\Customer
     */
    protected $customerSetup;

    /**
     * @param \Magento\CustomerSampleData\Model\Customer $customerSetup
     */
    public function __construct(
        \Magento\CustomerSampleData\Model\Customer $customerSetup
    ) {
        $this->customerSetup = $customerSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->customerSetup->install(['Magento_CustomerSampleData::fixtures/customer_profile.csv']);
    }
}
