<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CustomerSampleData;

use Magento\Framework\Setup;

/**
 * Class Setup
 */
class InstallSampleData implements SetupInterface
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
    public function install()
    {
        $this->customerSetup->run(['Magento_CustomerSampleData::Setup/OptionalData/fixtures/customer_profile.csv']);
    }
}
