<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData\Module\Customer\Setup;

use Magento\Tools\SampleData\TestLogger;

/**
 * Class CustomerTest
 */
class CustomerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        /** @var \Magento\Tools\SampleData\Module\Customer\Setup\Customer $customer */
        $customer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\Customer\Setup\Customer',
            ['logger' => TestLogger::factory()]
        );

        ob_start();
        $customer->run();
        $result = ob_get_clean();
        $this->assertContains('Installing customers', $result);
        $this->assertContains('.', $result);
    }
}
