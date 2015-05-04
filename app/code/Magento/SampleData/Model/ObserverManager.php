<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Module\ModuleListInterface;

/**
 * Class ObserverManager
 */
class ObserverManager
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var array
     */
    protected $observers;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ModuleListInterface $moduleList
     */
    public function __construct(ObjectManagerInterface $objectManager, ModuleListInterface $moduleList)
    {
        $this->objectManager = $objectManager;
        $this->moduleList = $moduleList;
    }

    /**
     * @return array
     */
    public function getObservers()
    {
        if (!is_null($this->observers)) {
            return $this->observers;
        }

        $this->observers = [];
        foreach ($this->moduleList->getNames() as $module) {
            $parts = explode('_', $module);
            $class = 'Magento\SampleData\Module\\' . $parts[1] . '\Observer';
            if (class_exists($class)) {
                $this->observers[] = $this->objectManager->get($class);
            }
        }

        return $this->observers;
    }
}
