<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Helper;

use Magento\Tools\SampleData\SetupInterface;

class PostInstaller
{
    /**
     * @var array
     */
    protected $setupList = [];

    /**
     * @var array
     */
    protected $installedModules;

    /**
     * @param SetupInterface $setupResource
     * @param int $sortOrder
     * @return $this
     */
    public function addSetupResource(SetupInterface $setupResource, $sortOrder = 10)
    {
        if (!isset($this->setupList[$sortOrder])) {
            $this->setupList[$sortOrder] = [];
        }
        $this->setupList[$sortOrder][] = $setupResource;
        return $this;
    }

    /**
     * Remove the installer from the list by its type
     *
     * @param string $resourceType
     * @return bool
     */
    public function removeSetupResourceType($resourceType)
    {
        foreach ($this->setupList as $orderNumber => $setupResources) {
            foreach ($setupResources as $resourceIndex => $resource) {
                if (get_class($resource) == $resourceType) {
                    unset($this->setupList[$orderNumber][$resourceIndex]);
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Adding module name to the list of modules, which installed their sample data
     *
     * @param string $moduleName
     * @return $this
     */
    public function addModule($moduleName)
    {
        $this->installedModules[] = $moduleName;
        return $this;
    }

    /**
     * Gets the list of modules, which installed their sample data
     *
     * @return array
     */
    public function getInstalledModuleList()
    {
        return $this->installedModules;
    }

    /**
     * Launch post install process
     *
     * @return $this
     */
    public function run()
    {
        foreach ($this->setupList as $setupResources) {
            foreach ($setupResources as $resource) {
                $resource->run();
            }
        }
        return $this;
    }
}
