<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\GiftRegistrySampleData\Setup;

use Magento\Framework\Setup;

/**
 * Class Setup
 * Launches setup of sample data for GiftRegistry module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var \Magento\GiftRegistrySampleData\Model\GiftRegistry $giftRegistry
     */
    protected $giftRegistry;

    /**
     * @param \Magento\GiftRegistrySampleData\Model\GiftRegistry $giftRegistry
     */
    public function __construct(\Magento\GiftRegistrySampleData\Model\GiftRegistry $giftRegistry)
    {
        $this->giftRegistry = $giftRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->giftRegistry->install(['Magento_GiftRegistrySampleData::fixtures/gift_registry.csv']);
    }
}
