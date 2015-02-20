<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tools\SampleData\Helper;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Class StoreManager
 */
class StoreManager
{
    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Store\Model\Group
     */
    protected $group;

    /**
     * @var \Magento\Store\Model\Website
     */
    protected $website;

    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Loading and caching of default website, store and store view
     *
     * @return bool
     */
    protected function loadStore()
    {
        $isLoaded = true;
        if (!$this->website) {
            $isLoaded = false;
            $websites = $this->storeManager->getWebsites();
            foreach ($websites as $website) {
                if ($website->getIsDefault()) {
                    $this->website = $website;
                    $this->group = $website->getDefaultGroup();
                    $this->store = $website->getDefaultStore();
                    $isLoaded = true;
                    break;
                }
            }
        }
        return $isLoaded;
    }

    /**
     * Load and return default store view
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        $this->loadStore();
        return $this->store;
    }

    /**
     * Loads default store view and returns its id
     *
     * @return int
     */
    public function getStoreId()
    {
        $this->loadStore();
        return $this->store->getId();
    }

    /**
     * Loads default website and returns its id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        $this->loadStore();
        return $this->website->getId();
    }

    /**
     * Loads default store and returns its id
     *
     * @return int
     */
    public function getGroupId()
    {
        $this->loadStore();
        return $this->group->getId();
    }
}
