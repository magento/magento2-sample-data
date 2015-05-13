<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData;

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
     * @var \Magento\User\Model\UserFactory
     */
    private $userFactory;

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
        \Magento\Tools\SampleData\Helper\Deploy $deploy,
        \Magento\Tools\SampleData\SetupFactory $setupFactory,
        \Magento\Tools\SampleData\Helper\PostInstaller $postInstaller,
        \Magento\Backend\Model\Auth\Session $session,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        $this->deploy = $deploy;
        $this->moduleList = $moduleList;
        $this->setupFactory = $setupFactory;
        $this->postInstaller = $postInstaller;
        $this->session = $session;
        $this->userFactory = $userFactory;
    }

    /**
     * Run installation in context of the specified admin user
     *
     * @param $userName
     * @param string $modules
     * @throws \Exception
     */
    public function run($userName, $modules = '')
    {
        set_time_limit(3600);

        /** @var \Magento\User\Model\User $user */
        $user = $this->userFactory->create()->loadByUsername($userName);
        if (!$user->getId()) {
            throw new \Exception('Invalid admin user provided');
        }

        $this->session->setUser($user);

        $this->deploy->run();

        $resources = $this->initResources($modules);
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
     * @param string $modules
     * @return array
     */
    private function initResources($modules = '')
    {
        $config = [];
        foreach (glob(__DIR__ . '/config/*.php') as $filename) {
            if (is_file($filename)) {
                $configPart = include $filename;
                $config = array_merge_recursive($config, $configPart);
            }
        }

        if ($modules) {
            $arrayModules = [];
            foreach (explode(' ', str_replace(',', ' ', $modules)) as $module) {
                $module = trim($module);
                $arrayModules[$module] = $module;
            }
            $config['setup_resources'] = array_intersect_key($config['setup_resources'], $arrayModules);
        }

        return isset($config['setup_resources']) ? $config['setup_resources'] : [];
    }
}
