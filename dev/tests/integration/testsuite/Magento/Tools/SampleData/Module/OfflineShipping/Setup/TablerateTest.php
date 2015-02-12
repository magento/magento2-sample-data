<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Module\OfflineShipping\Setup;

use Magento\Tools\SampleData\TestLogger;

/**
 * Class TablerateTest
 */
class TablerateTest  extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testRun()
    {
        /** @var \Magento\Tools\SampleData\Module\OfflineShipping\Setup\Tablerate $rate */
        $rate = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Module\OfflineShipping\Setup\Tablerate',
            ['logger' => TestLogger::factory()]
        );

        ob_start();
        $rate->run();
        $result = ob_get_clean();
        $this->assertContains('Installing Tablerate', $result);
        $this->assertContains('.........', $result);
    }
}
