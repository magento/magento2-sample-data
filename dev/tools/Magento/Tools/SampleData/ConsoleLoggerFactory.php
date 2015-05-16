<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData;

use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConsoleLoggerFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return \Magento\Setup\Model\LoggerInterface
     */
    public function create()
    {
        return $this->objectManager->create('Magento\Setup\Model\ConsoleLogger', ['output' => new ConsoleOutput()]);
    }
}
