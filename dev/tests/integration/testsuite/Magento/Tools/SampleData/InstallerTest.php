<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Tools\SampleData;

/**
 * Class InstallTest
 */
class InstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/User/_files/user_with_role.php
     */
    public function testLaunch()
    {
        $this->markTestSkipped('Skipped because of amount of time required for test.');

        $setupFactory = $this->getMockBuilder('Magento\Tools\SampleData\SetupFactory')->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();

        $setupFactory->expects($this->any())->method('create')
            ->will($this->returnCallback([$this, 'createSetupModel']));

        /** @var \Magento\Tools\SampleData\Installer $installer */
        $installer = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Tools\SampleData\Installer',
            [
                'data' => ['admin_username' => 'adminUser'],
                'logger' => TestLogger::factory(),
            ]
        );

        ob_start();
        $installer->launch();
        $result = ob_get_clean();
        $this->assertContains('Installing theme', $result);
        $this->assertContains('Installing customers', $result);
        $this->assertContains('Installing CMS pages', $result);
        $this->assertContains('Installing catalog attributes', $result);
        $this->assertContains('Installing categories', $result);
        $this->assertContains('Installing simple products', $result);
        $this->assertContains('Installing configurable products', $result);
        $this->assertContains('Installing downloadable products', $result);
        $this->assertContains('Installing bundle products', $result);
        $this->assertContains('Installing grouped products', $result);
        $this->assertContains('Installing Tablerate', $result);
        $this->assertContains('Installing virtual products', $result);
        $this->assertContains('Installing taxes', $result);
        $this->assertContains('Installing CMS blocks', $result);
        $this->assertContains('Installing orders', $result);
        $this->assertContains('Installing sales rules', $result);
        $this->assertContains('Installing product reviews', $result);
        $this->assertContains('Installing Widgets', $result);
    }
}
