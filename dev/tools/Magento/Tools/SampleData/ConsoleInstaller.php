<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData;

use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Console;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\ObjectManager\ConfigLoader;

/**
 * Sample data installation application
 */
class ConsoleInstaller implements \Magento\Framework\AppInterface
{
    /**
     * @var State
     */
    private $appState;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ConfigLoader
     */
    private $configLoader;

    /**
     * @var Console\Response
     */
    private $response;

    /**
     * @var array
     */
    private $data;

    /**
     * @param State $appState
     * @param ObjectManagerInterface $objectManager
     * @param ConfigLoader $configLoader
     */
    public function __construct(
        State $appState,
        ObjectManagerInterface $objectManager,
        ConfigLoader $configLoader,
        Console\Response $response,
        array $data = []
    ) {
        $this->appState = $appState;
        $this->objectManager = $objectManager;
        $this->configLoader = $configLoader;
        $this->response = $response;
        $this->data = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function launch()
    {
        $areaCode = 'adminhtml';
        $this->appState->setAreaCode($areaCode);
        $this->objectManager->configure($this->configLoader->load($areaCode));

        $consoleLogger = $this->objectManager->get('Magento\Tools\SampleData\ConsoleLoggerFactory')->create();
        $logger = $this->objectManager->get('Magento\Tools\SampleData\Logger');
        $logger->setSubject($consoleLogger);

        /** @var \Magento\Tools\SampleData\Installer $installer */
        $installer = $this->objectManager->get('Magento\Tools\SampleData\Installer');
        $userName = isset($this->data['admin_user']) ? $this->data['admin_user'] : '';
        $modules = isset($this->data['modules']) ? $this->data['modules'] : '';
        $installer->run($userName, $modules);

        return $this->response;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function catchException(Bootstrap $bootstrap, \Exception $exception)
    {
        return false;
    }
}
