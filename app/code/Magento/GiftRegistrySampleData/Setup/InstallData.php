<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleDataGiftRegistry\Setup;

use Magento\Framework\Setup;
use Magento\GiftRegistrySampleData\Model\GiftRegistry;

/**
 * Class Setup
 * Launches setup of sample data for GiftRegistry module
 */
class InstallData implements Setup\InstallDataInterface
{
    /**
     * @var GiftRegistry $giftRegistry
     */
    protected $giftRegistry;

    /**
     * @param GiftRegistry $giftRegistry
     */
    public function __construct(GiftRegistry $giftRegistry)
    {
        $this->giftRegistry = $giftRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function install(Setup\ModuleDataSetupInterface $setup, Setup\ModuleContextInterface $moduleContext)
    {
        $this->giftRegistry->run(['Magento_GiftRegistrySampleData::fixtures/gift_registry.csv']);
    }
}
