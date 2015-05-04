<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleData\Model;

/**
 * Model for installation Sample Data
 */
class Installer
{
    /**
     * @var Helper\Deploy
     */
    private $deploy;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @var SetupFactory
     */
    private $setupFactory;

    /**
     * @var Helper\PostInstaller
     */
    private $postInstaller;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param Helper\Deploy $deploy
     * @param SetupFactory $setupFactory
     * @param Helper\PostInstaller $postInstaller
     * @param \Magento\Backend\Model\Auth\Session $session
     */
    public function __construct(
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\SampleData\Helper\Deploy $deploy,
        \Magento\SampleData\Model\SetupFactory $setupFactory,
        \Magento\SampleData\Helper\PostInstaller $postInstaller,
        \Magento\Backend\Model\Auth\Session $session
    ) {
        $this->deploy = $deploy;
        $this->moduleList = $moduleList;
        $this->setupFactory = $setupFactory;
        $this->postInstaller = $postInstaller;
        $this->session = $session;
    }

    /**
     * Run installation in context of the specified admin user
     *
     * @param \Magento\User\Model\User $adminUser
     * @throws \Exception
     *
     * @return void
     */
    public function run(\Magento\User\Model\User $adminUser)
    {
        set_time_limit(3600);
        if (!$adminUser || !$adminUser->getId()) {
            throw new \Exception('Invalid admin user provided');
        }
        $this->session->setUser($adminUser);

        $this->deploy->run();

        $resources = $this->initResources();
        foreach ($this->moduleList->getNames() as $moduleName) {
            if (isset($resources[$moduleName])) {
                $resourceType = $resources[$moduleName];
                $this->setupFactory->create($resourceType)->run();
                $this->postInstaller->addModule($moduleName);
            }
        }

        $this->session->unsUser();
        $this->postInstaller->run();
    }

    /**
     * Init resources
     *
     * @return array
     */
    private function initResources()
    {
        $config = [];
        foreach (glob(__DIR__ . '/../config/*.php') as $filename) {
            if (is_file($filename)) {
                $configPart = include $filename;
                $config = array_merge_recursive($config, $configPart);
            }
        }
        return isset($config['setup_resources']) ? $config['setup_resources'] : [];
    }
}
