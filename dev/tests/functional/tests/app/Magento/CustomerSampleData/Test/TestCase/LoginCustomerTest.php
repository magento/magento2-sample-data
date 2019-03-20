<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\CustomerSampleData\Test\TestCase;

use Magento\Customer\Test\Fixture\Customer;
use Magento\Mtf\TestCase\Injectable;

/**
 * @group Sample_Data
 * @ZephyrId MAGETWO-33559
 */
class LoginCustomerTest extends Injectable
{
    /* tags */
    const MVP = 'yes';
    const TEST_TYPE = 'acceptance_test';
    const MFTF_MIGRATED = 'yes';
    /* end tags */

    /**
     * Login to customer account on Storefront.
     *
     * @param Customer $customer
     * @return void
     */
    public function test(Customer $customer)
    {
        // Steps
        $this->objectManager->create(
            'Magento\Customer\Test\TestStep\LoginCustomerOnFrontendStep',
            ['customer' => $customer]
        )->run();
    }
}
