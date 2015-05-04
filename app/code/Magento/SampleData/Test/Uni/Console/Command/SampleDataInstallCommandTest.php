<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Test\Unit\Console\Command;

use Magento\SampleData\Console\Command\SampleDataInstallCommand;
use Symfony\Component\Console\Tester\CommandTester;

class SampleDataInstallCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $objectManagerFactory = $this->getMock('Magento\Framework\App\ObjectManagerFactory', [], [], '', false);
        $objectManager = $this->getMock('Magento\Framework\ObjectManagerInterface', [], [], '', false);
        $sampleData = $this->getMock('Magento\Setup\Model\SampleData', [], [], '', false);
        $objectManagerFactory->expects($this->once())->method('create')->willReturn($objectManager);
        $commandTester = new CommandTester(new SampleDataInstallCommand($objectManagerFactory, $sampleData));
        $commandTester->execute(['admin']);
        $expectedMsg = 'Successfully installed sample data.' . PHP_EOL;
        $this->assertEquals($expectedMsg, $commandTester->getDisplay());
    }
}
