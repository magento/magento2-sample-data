<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Module\Customer;

use Magento\Tools\SampleData\SetupInterface;

/**
 * Class Setup
 */
class Setup implements SetupInterface
{
    /**
     * Setup class for customer
     *
     * @var Setup\Customer
     */
    protected $customerSetup;

    /**
     * @param Setup\Customer $customerSetup
     */
    public function __construct(
        Setup\Customer $customerSetup
    ) {
        $this->customerSetup = $customerSetup;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->customerSetup->run();
    }
}
