<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Test\Unit\Model;

use Magento\SampleData\Model\SampleData;

/**
 * Test Magento\Setup\Model\SampleData
 */
class SampleDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\SampleData\Model\SampleData
     */
    protected $sampleDataInstall;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * @var \Magento\SampleData\Helper\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $state;

    protected function setUp()
    {
        $this->directoryList = $this->getMock('Magento\Framework\App\Filesystem\DirectoryList', [], [], '', false);
        $this->state = $this->getMock('Magento\SampleData\Helper\State', [], [], '', false);
        $this->sampleDataInstall = new SampleData($this->directoryList, $this->state);
    }

    public function testIsInstalledSuccessfullyTrue()
    {
        $this->state->expects($this->once())
            ->method('getState')
            ->willReturn(\Magento\SampleData\Helper\State::STATE_FINISHED);
        $this->assertTrue($this->sampleDataInstall->isInstalledSuccessfully());
    }

    public function testIsInstalledSuccessfullyFalse()
    {
        $this->state->expects($this->once())
            ->method('getState')
            ->willReturn(\Magento\SampleData\Helper\State::STATE_NOT_STARTED);
        $this->assertFalse($this->sampleDataInstall->isInstalledSuccessfully());
    }

    public function testIsInstallationErrorTrue()
    {
        $this->state->expects($this->once())
            ->method('getState')
            ->willReturn(\Magento\SampleData\Helper\State::STATE_STARTED);
        $this->assertTrue($this->sampleDataInstall->isInstallationError());
    }

    public function testIsInstallationErrorFalse()
    {
        $this->state->expects($this->once())
            ->method('getState')
            ->willReturn(\Magento\SampleData\Helper\State::STATE_NOT_STARTED);
        $this->assertFalse($this->sampleDataInstall->isInstallationError());
    }

    public function testInstall()
    {
        $objectManager = $this->getMockForAbstractClass('Magento\Framework\ObjectManagerInterface', [], '', false);
        $logger = $this->getMockForAbstractClass('Magento\Framework\Setup\LoggerInterface', [], '', false);
        $sampleLogger = $this->getMock('Magento\SampleData\Model\Logger', [], [], '', false);
        $sampleLogger->expects($this->once())->method('setSubject')->with($logger);
        $objectManager->expects($this->at(0))->method('get')->willReturn($sampleLogger);
        $state = $this->getMock('Magento\Framework\App\State', [], [], '', false);
        $objectManager->expects($this->at(1))->method('get')->willReturn($state);
        $state->expects($this->once())->method('setAreaCode')->with('adminhtml');
        $configLoader = $this->getMockForAbstractClass(
            'Magento\Framework\ObjectManager\ConfigLoaderInterface',
            [],
            '',
            false
        );
        $objectManager->expects($this->at(2))->method('get')->willReturn($configLoader);
        $configLoader->expects($this->once())->method('load')->willReturn([]);
        $objectManager->expects($this->at(3))->method('configure')->with([]);
        $installer = $this->getMock('Magento\SampleData\Model\Installer', [], [], '', false);
        $objectManager->expects($this->at(4))->method('get')->willReturn($installer);
        $installer->expects($this->once())->method('run')->with('admin', []);
        $this->sampleDataInstall->install($objectManager, $logger, 'admin', []);
    }
}
