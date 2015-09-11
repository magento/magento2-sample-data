<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SampleData\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\LoggerInterface;
use Magento\SampleData\Helper\State;

/**
 * Sample data installer
 *
 * Serves as an integration point between Magento Setup application and Luma sample data component
 */
class SampleData
{
    /**
     * Filesystem Directory List
     *
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * Sample Data installation state
     *
     * @var State
     */
    private $state;

    /**
     * @param DirectoryList $directoryList
     * @param State $state
     */
    public function __construct(DirectoryList $directoryList, State $state)
    {
        $this->directoryList = $directoryList;
        $this->state = $state;
    }

    /**
     * Check whether installation of sample data was successful
     *
     * @return bool
     */
    public function isInstalledSuccessfully()
    {
        return State::STATE_FINISHED === $this->state->getState();
    }

    /**
     * Check whether there was unsuccessful attempt to install Sample data
     *
     * @return bool
     */
    public function isInstallationError()
    {
        return State::STATE_STARTED === $this->state->getState();
    }

    /**
     * Installation routine for creating sample data
     *
     * @param ObjectManagerInterface $objectManager
     * @param LoggerInterface $logger
     * @param string $userName
     * @param array $modules
     * @throws \Exception
     * @return void
     */
    public function install(
        ObjectManagerInterface $objectManager,
        LoggerInterface $logger,
        $userName,
        array $modules = []
    ) {
        /** @var \Magento\SampleData\Model\Logger $sampleDataLogger */
        $sampleDataLogger = $objectManager->get('Magento\SampleData\Model\Logger');
        $sampleDataLogger->setSubject($logger);

        $areaCode = 'adminhtml';
        /** @var \Magento\Framework\App\State $appState */
        $appState = $objectManager->get('Magento\Framework\App\State');
        $appState->setAreaCode($areaCode);
        /** @var \Magento\Framework\ObjectManager\ConfigLoaderInterface $configLoader */
        $configLoader = $objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
        $objectManager->configure($configLoader->load($areaCode));

        /** @var \Magento\SampleData\Model\Installer $installer */
        $installer = $objectManager->get('Magento\SampleData\Model\Installer');
        $installer->run($userName, $modules);
    }
}
