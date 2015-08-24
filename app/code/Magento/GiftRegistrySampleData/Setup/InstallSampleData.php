<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SampleDataGiftRegistry\Setup;

use Magento\Framework\Setup;
/**
 * Class Setup
 * Launches setup of sample data for GiftRegistry module
 */
class InstallSampleData implements SetupInterface
{
    /**
     * @var \Magento\GiftRegistrySampleData\Model\GiftRegistry $giftRegistry
     */
    protected $giftRegistry;

    public function __construct(
        \Magento\GiftRegistrySampleData\Model\GiftRegistry $giftRegistry
    ) {
        $this->giftRegistry = $giftRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function install()
    {
        $this->giftRegistry->run(['Magento_GiftRegistry::fixtures/gift_registry.csv']);
    }
}
